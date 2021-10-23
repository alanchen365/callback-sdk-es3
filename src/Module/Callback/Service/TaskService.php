<?php

namespace App\Module\Callback\Service;

use App\Constant\ResultConst;
use App\Module\Callback\CallbackConstant;
use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Model\TaskModel;
use App\Module\Callback\Type\TaskType;
use App\Module\Callback\Validate\TaskValidate;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\HttpClient\Bean\Response;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\DbManager;
use Es3\Call\Curl;
use Es3\Exception\ErrorException;
use Es3\Exception\InfoException;
use Es3\Exception\NoticeException;
use Es3\Exception\WaringException;
use Es3\Lock\FileLock;
use Es3\Utility\Random;
use function Symfony\Component\String\s;

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

            //response_success_condition 和 response_success_value 是否都传递了
            $successCondition = $push['response_success_condition'];
            $successValue = $push['response_success_value'];

            if (superEmpty($successCondition)) {
                throw new NoticeException(7013, "system没有配置response_success_condition字段");
            }

            if (superEmpty($successValue)) {
                throw new NoticeException(7014, "system没有配置response_success_value字段");
            }

            /** 获取task code */
            $taskCode = $task->getTaskCode();
            if (superEmpty($taskCode)) {
                $taskCode = md5(uniqid(microtime(true), true));
            }
            $taskCode = md5($taskCode . $push['system_code'] ?? null);
            $headers = array_merge(json_decode($push['request_header'], true) ?? [], $task->getRequestHeader() ?? []);

            $tasks[] = [
                'task_code' => md5($taskCode),
                'system_id' => $push['system_id'],
                'domain' => $push['domain'],
                'path' => $push['path'],
                'is_async' => $push['is_async'],
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

    public function main($task)
    {
        $logService = new TaskService();

        $taskCode = $task['task_code'] ?? Random::makeUUIDV4();
        $response = null;
        try {
            $lock = FileLock::get($taskCode);
            $lock->lock();

            /** 更新请求日志 */
            $taskParams = ['request_count' => QueryBuilder::inc(1)];
            TaskModel::create()->update($taskParams, ['id' => $task['id']]);

            /** 参数整理 */
            $response = null;
            $result = '';
            $startTime = microtime(true);

            try {
                $url = $task['domain'] . $task['path'];

                $method = $task['request_method'];
                $headers = json_decode($task['request_header'], true);
                $headers = superEmpty($headers) ? [] : $headers;
                $params = $task['request_param'];

                $curl = new Curl($url);
                $curl->setTimeout(30);
                $curl->setIs200(false);

                /** 调用方式 */
                if ('JSON' == $task['request_type']) {
                    $curl->setHeader('Content-type', 'application/json;charset=utf-8');
                }

                /** 是否为跨环境调用 */
                if (strtoupper(env()) != strtoupper($task['env'])) {
                    throw new WaringException(7110, '环境存在差异');
                }

                switch (strtolower($method)) {

                    case 'get':
                        $response = $curl->get();
                        break;
                    case 'post':
                        $response = $curl->post($params, $headers);
                        break;
                    case 'put':
                        $response = $curl->put($params, $headers);
                        break;
                    case 'delete':
                        $response = $curl->delete();
                        break;
                }
            } catch (\Throwable $throwable) {
                $result = "请求失败 code:" . $throwable->getCode() . ' message:' . $throwable->getMessage();
            }

            /** 判断请求成功还是失败 */
            $duration = round(microtime(true) - $startTime, 5);
            if ($response instanceof Response) {
                $requestBody = json_decode($response->getBody(), true);

                /** 获取执行结果 */
                $responseBusinessKeyCode = $task['response_key_code'];
                $responseKeyMsg = $task['response_key_msg'];

                /** 获取code来判断请求是否发送成功 如果有 . 就分割 并且重获取该值 */
                if (strstr($responseBusinessKeyCode, '.')) {
                    $responseBusinessKeyCodeArr = explode('.', $responseBusinessKeyCode);
                    $requestCode = $requestBody;
                    foreach ((array)$responseBusinessKeyCodeArr as $key) {
                        $requestCode = $requestCode[$key] ?? null;
                    }

                    if (is_array($requestCode)) {
                        $requestCode = null;
                    }
                } else {
                    $requestCode = $requestBody[$responseBusinessKeyCode] ?? null;
                }
                
                /** 获取msg来判断请求是否发送成功 如果有 . 就分割 并且重获取该值 */
                if (strstr($responseKeyMsg, '.')) {
                    $responseKeyMsgArr = explode('.', $responseKeyMsg);
                    $result = $requestBody;
                    foreach ((array)$responseKeyMsgArr as $key) {
                        $result = $result[$key] ?? null;
                    }
                } else {
                    $result = $requestBody[$responseKeyMsg] ?? null;
                }

                /** 如果返回值是不是字符串 就强制换算 */
                $result = is_string($result) ? $result : json_encode($result);

                /** 判断请求失败还是成功 */
                $successCondition = $task['response_success_condition'];
                $successValue = $task['response_success_value'];

                # 响应成功条件 GT大于，GE大于或等于，NE是不等于，EQ是等于，LT小于，LE小于或等于
                $isSuccess = false;
                switch ($successCondition) {
                    case 'GT':
                        $isSuccess = $requestCode > $successValue ? true : false;;
                        break;
                    case 'GE':
                        $isSuccess = $requestCode >= $successValue ? true : false;;
                        break;
                    case 'NE':
                        $isSuccess = $requestCode != $successValue ? true : false;;
                        break;
                    case 'EQ':
                        $isSuccess = $requestCode == $successValue ? true : false;;
                        break;
                    case 'LT':
                        $isSuccess = $requestCode < $successValue ? true : false;;
                        break;
                    case 'LE':
                        $isSuccess = $requestCode <= $successValue ? true : false;;
                        break;
                    default:
                        $isSuccess = false;
                }

                $status = $isSuccess ? 'SUCCESS' : 'FAIL';
                /** 写入最终状态 */
                $responseParams = ['result' => $result, 'status' => $status, 'response_body' => $response->getBody(), 'response_http_code' => $response->getStatusCode(), 'response_business_code' => $requestCode, 'request_duration' => $duration,];
            } else {
                /** 如果curl没有发送成功 标记为error状态 */
                $responseParams = ['result' => $result, 'status' => 'ERROR', 'request_duration' => $duration];
            }

            TaskModel::create()->update($responseParams, ['id' => $task['id']]);

            /** 解锁 */
            $lock->unlock();
        } catch (ErrorException $e) {
            Logger::getInstance()->log($e->getMessage(), Logger::LOG_LEVEL_ERROR, 'callback-process');
            $lock instanceof \swoole_lock ? $lock->unlock() : null;
        }
        /** 为防止有任务不断重复调用 间隔0.1秒调用 */
        usleep(1000000);
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
