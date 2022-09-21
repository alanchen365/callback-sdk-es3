<?php

namespace App\Module\Callback\Process;

use App\Constant\EnvConst;
use App\Module\Callback\CallbackConstant;
use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Queue\TaskErrorQueue;
use App\Module\Callback\Queue\TaskFailQueue;
use App\Module\Callback\Queue\TaskInvalidQueue;
use App\Module\Callback\Service\GatewayService;
use App\Module\Callback\Service\TaskService;
use App\Module\Callback\Util\EnvUtil;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Config;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;
use Es3\Trace;
use Swoole\Process;
use EasySwoole\ORM\DbManager;

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
        if (!EnvUtil::isRun()) {
            return;
        }

        while (true) {
            $needWait = false;
            try {
                /** 查询数据库，获得需要发送的消息 */
                $taskDao = new TaskDao();
                $taskService = new TaskService();

                /** 获取未推送的任务 */
                try {
                    DbManager::getInstance()->startTransaction();

                    $taskList = $taskDao->taskList(['INVALID'], false);
                    
                    DbManager::getInstance()->commit();
                } catch (\Throwable $throwable) {
                    DbManager::getInstance()->rollback();
                }

                if (!superEmpty($taskList)) {
                    foreach ($taskList as $key => $task) {
                        $taskService->main($task);
                    }
                } else {
                    $needWait = true;
                }
            } catch (\Throwable $throwable) {
                $needWait = true;
                $msg = "系统发生异常:" . $throwable->getCode() . ' ' . $throwable->getMessage();
                Logger::getInstance()->log($msg, Logger::LOG_LEVEL_ERROR, 'callback_task');
            }
            $this->sleep($needWait);
        }
    }

    public function sleep(bool $needWait)
    {
        if ($needWait) {
            sleep(5);
        }
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
        Logger::getInstance()->log($throwable->getMessage(), Logger::LOG_LEVEL_ERROR, 'callback-process');
    }
}
