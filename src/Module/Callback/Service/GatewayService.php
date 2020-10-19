<?php

namespace App\Module\Callback\Service;

use App\Module\Callback\Dao\ApiDao;
use App\Module\Callback\Type\TaskType;
use App\Module\Callback\Validate\TaskValidate;
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
        } catch (\Throwable $throwable) {
            !$isTransaction ? DbManager::getInstance()->rollback() : null;
            throw new WaringException($throwable->getCode(), '任务投递异常:' . $throwable->getMessage());
        }
    }
}
