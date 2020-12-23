<?php
//
//namespace App\Module\Callback\Process;
//
//use App\Constant\EnvConst;
//use App\Module\Callback\CallbackConstant;
//use App\Module\Callback\Dao\TaskDao;
//use App\Module\Callback\Queue\TaskErrorQueue;
//use App\Module\Callback\Queue\TaskFailQueue;
//use App\Module\Callback\Queue\TaskInvalidQueue;
//use App\Module\Callback\Service\GatewayService;
//use App\Module\Callback\Service\TaskService;
//use EasySwoole\Component\Process\AbstractProcess;
//use EasySwoole\Component\Process\Config;
//use EasySwoole\Component\Timer;
//use EasySwoole\EasySwoole\Logger;
//use EasySwoole\EasySwoole\Task\TaskManager;
//use Es3\Trace;
//use Swoole\Process;
//
//class TaskErrorProcess extends AbstractProcess
//{
//    public static function getConf(): Config
//    {
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setArg([]);   //传参
//        $processConfig->setRedirectStdinStdout(false);  //是否重定向标准io
//        $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);  //设置管道类型
//        $processConfig->setEnableCoroutine(true);   //是否自动开启协程
//        $processConfig->setMaxExitWaitTime(3);  //最大退出等待时间
//
//        return $processConfig;
//    }
//
//    protected function run($arg)
//    {
//        /** 2分钟重新通信一次 */
//        \EasySwoole\Component\Timer::getInstance()->loop(2 * 60 * 1000, function () {
//
//            $gatewayService = new GatewayService();
//            $gatewayService->call(['ERROR', 'RUN']);
//
//            $job = TaskErrorQueue::getInstance()->consumer()->pop();
////            if ($job) {
////                /** 剩下的队列都弹完 */
//////                while (true) {
//////
//////                    $job = TaskErrorQueue::getInstance()->consumer()->pop();
//////                    if (!$job) {
//////                        break;
//////                    }
//////                }
////
//////                $gatewayService = new GatewayService();
//////                $gatewayService->call(['ERROR']);
////            }
//        });
//    }
//
//    protected function onPipeReadable(Process $process)
//    {
//        /*
//         * 该回调可选
//         * 当有主进程对子进程发送消息的时候，会触发的回调，触发后，务必使用
//         * $process->read()来读取消息
//         */
//    }
//
//    protected function onShutDown()
//    {
//        /*
//         * 该回调可选
//         * 当该进程退出的时候，会执行该回调
//         */
//    }
//
//    protected function onException(\Throwable $throwable, ...$args)
//    {
//        /*
//         * 该回调可选
//         * 当该进程出现异常的时候，会执行该回调
//         */
//    }
//}
