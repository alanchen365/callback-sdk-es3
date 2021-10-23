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
     * @param int $isAsync 0:所有任务 1:异步任务 2:同步任务
     * @return array
     */
    public function taskList(array $status, int $isAsync = 0): array
    {
        $status = implode("','", $status);

        $where = '';
        if ($isAsync == 1) {
            $where = "AND task.`is_async` = 1";
        }
        if ($isAsync == 2) {
            $where = "AND task.`is_async` = 0";
        }

        $env = strtoupper(env());
        $sql = "SELECT
                task.`id`,
                task.`task_code`,
                # task.`domain`,
                task.`path`,
                task.`request_header`,
                task.`request_method`,
                task.`request_type`,
                task.`request_param`,
                task.`request_count`,
                task.`status`,
                task.`create_time`,
                task.`request_duration`,
                #task.`env`,
                system.system_name,
                #system.request_header,
                system.domain,
                system.response_success_value,
                system.response_key_msg,
                system.response_key_code,
                system.response_success_condition,
                system.`env` 
            FROM
                `callback_task` task
                LEFT JOIN `callback_system` system ON task.system_id = system.id 
            WHERE
                task.`status` IN ( '$status' )
                AND system.`env` = '{$env}'
                {$where}
            ORDER BY task.request_count ASC";

        $list = $this->query($sql);
        return $list;
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
