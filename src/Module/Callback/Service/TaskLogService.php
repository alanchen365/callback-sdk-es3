<?php

namespace App\Module\Callback\Service;

use App\Module\Callback\Dao\TaskDao;
use App\Module\Callback\Dao\TaskLogDao;
use App\Module\Callback\Model\TaskLogModel;
use App\Module\Callback\Model\TaskModel;
use EasySwoole\HttpClient\Bean\Response;
use Es3\Call\Curl;
use Es3\Exception\ErrorException;
use Es3\Exception\WaringException;

class TaskLogService extends BaseCallbackService
{
    public function __construct()
    {
        $this->setDao(new TaskLogDao());
    }

    public function call(TaskModel $task, TaskLogModel $taskLog): void
    {
        /** 参数整理 */
        $response = null;
        $extra = '';
        $startTime = microtime(true);

        try {
            $url = $task->getAttr('domain') . $task->getAttr('path');

            $method = $task->getAttr('request_method');
            $headers = json_decode($task->getAttr('request_header'), true);
            $headers = superEmpty($headers) ? [] : $headers;
            $params = $task->getAttr('request_param');

            $curl = new Curl($url);
            $curl->setIs200(false);

            /** 调用方式 */
            if ('JSON' == $task->getAttr('request_type')) {
                $curl->setHeader('Content-type', 'application/json;charset=utf-8');
            }

            /** 是否为跨环境调用 */
            if (strtoupper(env()) != strtoupper($task->getAttr('env'))) {
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
            $extra = "请求失败 code:" . $throwable->getCode() . ' message:' . $throwable->getMessage();
        }

        /** 写入最终日志 */
        $duration = round(microtime(true) - $startTime, 5);
        if ($response instanceof Response) {
            $requestBody = json_decode($response->getBody(), true);

            $responseParams = ['response_body' => $response->getBody(), 'response_http_code' => $response->getStatusCode(), 'response_business_code' => $requestBody['code'] ?? null, 'request_duration' => $duration,];
            $params = ['extra' => json_encode($response->toArray())];
            $params = array_merge($params, $responseParams);

            $task->update($responseParams);
        } else {
            /** 如果curl没有发送成功 标记为error状态 */
            $params = ['extra' => $extra, 'status' => 'ERROR', 'request_duration' => $duration];
        }

        /** 写入日志 */
        $taskLog->update($params);
    }

    public function isSuccess(string $taskCodes, TaskModel $task, int $taskLogId)
    {
        $taskLog = $this->getDao()->get(['id' => $taskLogId]);

        $businessCode = $taskLog->getAttr('response_business_code');
        $taskLogStatus = $taskLog->getAttr('status');

        /** 如果是error状态 说明请求没有到达对方那里*/
        if ($taskLogStatus == 'ERROR') {
            $status = 'ERROR';
        } else {
            $status = 'FAIL';
            if ($businessCode >= 100000) {
                $status = 'SUCCESS';
            }
        }

        $params = ['status' => $status];

        $task->update($params);
        $taskLog->update($params);
    }

    /**
     * dao
     * @return TaskLogDao
     */
    public function getDao(): TaskLogDao
    {
        return $this->dao;
    }
}
