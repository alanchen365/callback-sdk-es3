<?php

namespace App\Module\Callback\Service;

use App\Constant\ResultConst;
use App\Module\Callback\CallbackConstant;
use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Dao\TaskLogDao;
use App\Module\Callback\Model\TaskModel;
use App\Module\Callback\Type\TaskType;
use App\Module\Callback\Validate\TaskValidate;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\DbManager;
use Es3\Call\Curl;
use Es3\Exception\ErrorException;
use Es3\Exception\NoticeException;
use Es3\Exception\WaringException;
use Es3\Lock\FileLock;

class TaskService extends BaseCallbackService
{
    public function __construct()
    {
        $this->setDao(new TaskDao());
    }

    /**
     * 循环投递投递任务
     * @param array $pushParam
     */
    public function push(TaskType $task): void
    {
        /** 推送系统列表 */
        $pushList = (new SystemApiService)->pushList($task->getApiCode());
        if (superEmpty($pushList)) {
            throw new WaringException(7101, '未找到api:' . $task->getApiCode() . '的推送配置');
        }

        /** 拼接整理参数 */
        $tasks = [];
        foreach ($pushList as $key => $push) {

            /** 获取task code */
            $taskCode = $task->getTaskCode();
            if (superEmpty($taskCode)) {
                $taskCode = md5(uniqid(microtime(true), true));
            }
            $taskCode = md5($taskCode . $push['system_code'] ?? null);
            $headers = array_merge(json_decode($push['request_header'], true) ?? [], $task->getRequestHeader() ?? []);

            $tasks[] = [
                'task_code' => md5($taskCode),
                'domain' => $push['domain'],
                'path' => $push['path'],
                'request_header' => json_encode($headers),
                'request_method' => $push['request_method'],
                'request_type' => $push['request_type'],
                'request_param' => json_encode($task->getRequestParams()),
                'env' => strtoupper(env()),
            ];
        }

        if (superEmpty($tasks)) {
            throw new WaringException(7102, '未找到推送配置');
        }

        /** codes 是否重复 */
        $taskCodes = array_column($tasks, 'task_code') ?? [];
        $taskList = $this->getDao()->getAll(['task_code' => [$taskCodes, 'IN']]);
        $taskList = $taskList[ResultConst::RESULT_LIST_KEY] ?? [];

        if (!superEmpty($taskList)) {
            $taskCode = array_column($taskList, 'task_code');
            $taskCode = implode(',', $taskCode);
            throw new NoticeException(7011, "任务编码 " . $taskCode . " 已存在 请更换");
        }

        /** 是否必填 */
        $taskValidate = new TaskValidate();
        $taskValidate->save($tasks);

        /** 投递任务 */
        $this->getDao()->insertAll($tasks);
    }

    public function main(TaskModel $task)
    {
        $logService = new TaskLogService();
        $taskLogDao = new TaskLogDao();

        $taskCode = $task->getAttr('task_code');
        $response = null;
        try {
            $lock = FileLock::get($taskCode);
            $lock->lock();

            /** 更新请求日志 */
            $taskParams = ['status' => 'RUN', 'request_count' => QueryBuilder::inc(1)];
            $task->update($taskParams);

            $taskLog = $taskLogDao->createLog($task);

            /** 发送请求  */
            $logService->call($task, $taskLog);

            /** 判断最终请求是否成功 */
            $logService->isSuccess($taskCode, $task, $taskLog->getAttr('id'));

            /** 保留最近10条log */
            $taskLogDao->clearLog($taskCode, CallbackConstant::SAVE_LOG_COUNT);

            /** 解锁 */
            $lock->unlock();
        } catch (ErrorException $e) {
            $lock instanceof \swoole_lock ? $lock->unlock() : null;
        }
    }

    /**
     * dao
     * @return TaskDao
     */
    public function getDao(): TaskDao
    {
        return $this->dao;
    }
}
