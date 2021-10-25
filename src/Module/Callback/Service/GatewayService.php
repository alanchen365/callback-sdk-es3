<?php

namespace App\Module\Callback\Service;

use App\Constant\EnvConst;
use App\Module\Callback\CallbackConstant;
use App\Module\Callback\Dao\ApiDao;
use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Type\TaskType;
use App\Module\Callback\Validate\TaskValidate;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\DbManager;
use Es3\Exception\WaringException;

class GatewayService
{
    /**
     * 投递任务 同步
     */
    public function push(array $taskList)
    {
        /** 是否开始事物 */
        $isTransaction = DbManager::getInstance()->invoke(function (ClientInterface $client) {
            return DbManager::isInTransaction($client);
        });

        /** 外面没有开事物 里面就开 外面开里面就不开 */
        try {
            !$isTransaction ? DbManager::getInstance()->startTransaction() : null;

            $taskService = new TaskService();

            /** 循环投递任务 */
            foreach ($taskList as $key => $task) {
                $taskService->push($task);
            }

            !$isTransaction ? DbManager::getInstance()->commit() : null;

            /** 任务投递成功后发布 */
            $redis = \EasySwoole\RedisPool\Redis::defer(EnvConst::REDIS_KEY);
            $redis->publish(redisKey(CallbackConstant::REDIS_CALL_CHANNEL), 'INVALID');
        } catch (\Throwable $throwable) {
            !$isTransaction ? DbManager::getInstance()->rollback() : null;
            throw new WaringException($throwable->getCode(), '任务投递异常:' . $throwable->getMessage());
        }
    }

//    /**
//     * 开始调用任务
//     */
//    public function call(array $status)
//    {
//        $taskDao = new TaskDao();
//        $taskService = new TaskService();
//
//        $taskList = $taskDao->taskList($status);
//        if (superEmpty($taskList)) {
//            return;
//        }
//
//        foreach ($taskList as $key => $task) {
//            $taskService->main($task);
//        }
//    }
}
