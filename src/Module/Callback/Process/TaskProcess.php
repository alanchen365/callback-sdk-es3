<?php

namespace App\Module\Callback\Process;

use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Service\TaskService;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Config;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;
use Es3\Trace;
use Swoole\Process;

class TaskProcess extends AbstractProcess
{
    public static function getConf(): Config
    {
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setArg([]);   //传参
        $processConfig->setRedirectStdinStdout(false);  //是否重定向标准io
        $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);  //设置管道类型
        $processConfig->setEnableCoroutine(true);   //是否自动开启协程
        $processConfig->setMaxExitWaitTime(3);  //最大退出等待时间

        return $processConfig;
    }

    protected function run($arg)
    {
        $taskDao = new TaskDao();
        $taskService = new TaskService();

        /** 重新调用时间推算 */
        $firstLoop = isProduction() ? 1 * 1000 : 10 * 1000;

        echo "回调服务已启动\n";
        /** 首次调用的 和 网络连接失败的 */
        \EasySwoole\Component\Timer::getInstance()->loop($firstLoop, function () use ($taskDao, $taskService) {
            try {
                $taskList = $taskDao->taskList(['INVALID', 'ERROR']);
                if (superEmpty($taskList)) {
                    return;
                }

                foreach ($taskList as $key => $task) {
                    TaskManager::getInstance()->async(function () use ($taskService, $task) {
                        $taskService->main($task);
                    });
                }
            } catch (\Throwable $throwable) {
                Logger::getInstance()->waring($throwable->getMessage(), 'TaskProcess');
            }
        });
    }

    protected function onPipeReadable(Process $process)
    {
        /*
         * 该回调可选
         * 当有主进程对子进程发送消息的时候，会触发的回调，触发后，务必使用
         * $process->read()来读取消息
         */
    }

    protected function onShutDown()
    {
        /*
         * 该回调可选
         * 当该进程退出的时候，会执行该回调
         */
    }

    protected function onException(\Throwable $throwable, ...$args)
    {
        /*
         * 该回调可选
         * 当该进程出现异常的时候，会执行该回调
         */
    }
}
