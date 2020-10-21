<?php

namespace App\Module\Callback\Dao;

use App\Constant\ResultConst;
use App\Module\Callback\Model\TaskLogModel;
use App\Module\Callback\Model\TaskModel;
use EasySwoole\HttpClient\Bean\Response;

class TaskLogDao extends BaseCallbackDao
{
    public function __construct()
    {
        $this->setModel(new TaskLogModel());
    }

    public function createLog(TaskModel $task): TaskLogModel
    {
        /** 参数整理 */
        $url = $task->getAttr('domain') . $task->getAttr('path');

        $params = array_save($task->toArray(), ['task_code', 'request_header', 'request_method', 'request_type', 'request_param', 'request_param']);
        $params = array_merge($params, ['url' => $url, 'status' => 'RUN']);

        $id = $this->save($params);
        return $this->get(['id' => $id]);
    }

    public function clearLog(string $taskCode, int $keep = 10)
    {
        /** 保留最近10条日志 */
        $list = $this->getAll(['task_code' => $taskCode], [], ['id' => 'asc']);
        $list = $list[ResultConst::RESULT_LIST_KEY] ?? [];
        $ids = array_column($list, 'id');

        if (superEmpty($ids)) {
            return;
        }

        if (count($ids) < $keep) {
            return;
        }

        $ids = array_slice($ids, 0, $keep);
        if (superEmpty($ids)) {
            return;
        }

        $this->delete($ids);
    }

    /**
     * model
     * @return TaskLogModel
     */
    public function getModel(): TaskLogModel
    {
        return $this->model;
    }
}
