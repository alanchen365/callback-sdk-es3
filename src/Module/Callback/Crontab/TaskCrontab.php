<?php

namespace App\Module\Callback\Crontab;

use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Service\TaskService;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;

class TaskCrontab extends AbstractCronTask
{
    /**
     * 执行规则
     */
    public static function getRule(): string
    {
        return '*/2 * * * *';
    }

    /**
     * 任务名称
     */
    public static function getTaskName(): string
    {
        return 'TaskCrontab';
    }

    /**
     * 两分钟执行一次定时任务
     */
    public function run(int $taskId, int $workerIndex)
    {
        $taskDao = new TaskDao();
        $taskService = new TaskService();

        echo "回调重调服务已启动\n";
        try {
            $taskList = $taskDao->taskList(['FAIL']);
            if (superEmpty($taskList)) {
                return;
            }

            foreach ($taskList as $key => $task) {
                TaskManager::getInstance()->async(function () use ($taskService, $task) {
                    $taskService->main($task);
                });
            }
        } catch (\Throwable $throwable) {
            Logger::getInstance()->waring($throwable->getMessage(), 'TaskCrontab');
        }
    }

    /**
     * 出现异常
     */
    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}
