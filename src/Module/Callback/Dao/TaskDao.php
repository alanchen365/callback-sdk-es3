<?php

namespace App\Module\Callback\Dao;

use App\Constant\ResultConst;
use App\Module\Callback\Model\TaskModel;
use EasySwoole\Mysqli\QueryBuilder;

class TaskDao extends BaseCallbackDao
{
    public function __construct()
    {
        $this->setModel(new TaskModel());
    }

    /**
     * 获取发送任务列表
     * @param array $status
     * @return array
     */
    public function taskList(array $status): array
    {
        $list = $this->getAll(['status' => [$status, 'IN']]);
        return $list[ResultConst::RESULT_LIST_KEY] ?? [];
    }
    
    /**
     * model
     * @return TaskModel
     */
    public function getModel(): TaskModel
    {
        return $this->model;
    }
}
