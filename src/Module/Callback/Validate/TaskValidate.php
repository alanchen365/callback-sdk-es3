<?php

namespace App\Module\Callback\Validate;

use App\Module\Callback\Type\TaskType;
use EasySwoole\Validate\Validate;
use Es3\Exception\InfoException;
use Es3\Exception\WaringException;

class TaskValidate
{
    protected static $alias = [
        'id' => '',
        'system_code' => '所属系统code',
        'domain' => '域名',
        'path' => '请求路径',
        'request_header' => '请求头',
        'is_async' => '是否启用异步进程 0:否 1:是',
        'request_method' => '请求方法',
        'request_type' => '请求类型',
        'request_param' => '请求body',
        'request_count' => '请求次数',
        'status' => '综合判定请求结果',
        'next_time' => '下次请求时间',
        'create_time' => '创建时间',
        'update_time' => '最后更新时间',
    ];

    public function save(array $tasks): ?Validate
    {
        /** 必填是否正确 */
        foreach ($tasks as $key => $task) {
            $validate = new Validate();

            $validate->addColumn('domain', TaskValidate::$alias['domain'])->required();
            $validate->addColumn('path', TaskValidate::$alias['path'])->required();
            $validate->addColumn('request_method', TaskValidate::$alias['request_method'])->required()->inArray(['GET', 'POST', 'PUT', 'DELETE']);
            $validate->addColumn('request_type', TaskValidate::$alias['request_type'])->required()->inArray(['JSON']);

            if ($validate->validate($task) == false) {
                throw new WaringException(7002, "{$validate->getError()->getField()}@{$validate->getError()->getFieldAlias()}:{$validate->getError()->getErrorRuleMsg()}");
            }
        }

        // 投递任务是否重复
        return null;
    }

    /**
     * 投递任务是否重复
     */
    public function isRepeat()
    {
    }
}
