<?php

namespace App\Module\Callback\Validate;

use EasySwoole\Validate\Validate;

class TaskLogValidate
{
    protected static $alias = [
        'id' => '',
        'system_code' => '所属系统code',
        'url' => '请求地址',
        'request_header' => '请求头',
        'request_method' => '请求方法',
        'request_type' => '请求类型',
        'request_body' => '请求body',
        'request_duration' => '请求耗时',
        'response_body' => '响应body',
        'response_http_code' => '响应http code',
        'response_business_code' => '响应业务编码',
        'status' => '综合判定请求结果',
        'create_time' => '创建时间',
        'update_time' => '最后更新时间',
        
    ];

    public function index(array $params): ?Validate
    {
        $validate = new Validate();

        // $validate->addColumn('id', TaskLogValidate::$alias['id']);
        // $validate->addColumn('system_code', TaskLogValidate::$alias['system_code']);
        // $validate->addColumn('url', TaskLogValidate::$alias['url']);
        // $validate->addColumn('request_header', TaskLogValidate::$alias['request_header']);
        // $validate->addColumn('request_method', TaskLogValidate::$alias['request_method']);
        // $validate->addColumn('request_type', TaskLogValidate::$alias['request_type']);
        // $validate->addColumn('request_body', TaskLogValidate::$alias['request_body']);
        // $validate->addColumn('request_duration', TaskLogValidate::$alias['request_duration']);
        // $validate->addColumn('response_body', TaskLogValidate::$alias['response_body']);
        // $validate->addColumn('response_http_code', TaskLogValidate::$alias['response_http_code']);
        // $validate->addColumn('response_business_code', TaskLogValidate::$alias['response_business_code']);
        // $validate->addColumn('status', TaskLogValidate::$alias['status']);
        // $validate->addColumn('create_time', TaskLogValidate::$alias['create_time']);
        // $validate->addColumn('update_time', TaskLogValidate::$alias['update_time']);
        
        return $validate;
    }

    public function get(array $params): ?Validate
    {
        $validate = new Validate();

        // $validate->addColumn('id', TaskLogValidate::$alias['id']);
        // $validate->addColumn('system_code', TaskLogValidate::$alias['system_code']);
        // $validate->addColumn('url', TaskLogValidate::$alias['url']);
        // $validate->addColumn('request_header', TaskLogValidate::$alias['request_header']);
        // $validate->addColumn('request_method', TaskLogValidate::$alias['request_method']);
        // $validate->addColumn('request_type', TaskLogValidate::$alias['request_type']);
        // $validate->addColumn('request_body', TaskLogValidate::$alias['request_body']);
        // $validate->addColumn('request_duration', TaskLogValidate::$alias['request_duration']);
        // $validate->addColumn('response_body', TaskLogValidate::$alias['response_body']);
        // $validate->addColumn('response_http_code', TaskLogValidate::$alias['response_http_code']);
        // $validate->addColumn('response_business_code', TaskLogValidate::$alias['response_business_code']);
        // $validate->addColumn('status', TaskLogValidate::$alias['status']);
        // $validate->addColumn('create_time', TaskLogValidate::$alias['create_time']);
        // $validate->addColumn('update_time', TaskLogValidate::$alias['update_time']);
        
        return $validate;
    }

    public function save(array $params): ?Validate
    {
        $validate = new Validate();

        // $validate->addColumn('id', TaskLogValidate::$alias['id']);
        // $validate->addColumn('system_code', TaskLogValidate::$alias['system_code']);
        // $validate->addColumn('url', TaskLogValidate::$alias['url']);
        // $validate->addColumn('request_header', TaskLogValidate::$alias['request_header']);
        // $validate->addColumn('request_method', TaskLogValidate::$alias['request_method']);
        // $validate->addColumn('request_type', TaskLogValidate::$alias['request_type']);
        // $validate->addColumn('request_body', TaskLogValidate::$alias['request_body']);
        // $validate->addColumn('request_duration', TaskLogValidate::$alias['request_duration']);
        // $validate->addColumn('response_body', TaskLogValidate::$alias['response_body']);
        // $validate->addColumn('response_http_code', TaskLogValidate::$alias['response_http_code']);
        // $validate->addColumn('response_business_code', TaskLogValidate::$alias['response_business_code']);
        // $validate->addColumn('status', TaskLogValidate::$alias['status']);
        // $validate->addColumn('create_time', TaskLogValidate::$alias['create_time']);
        // $validate->addColumn('update_time', TaskLogValidate::$alias['update_time']);
        
        return $validate;
    }

    public function update(array $params): ?Validate
    {
        $validate = new Validate();

        $validate->addColumn('id', TaskLogValidate::$alias['id'])->required()->notEmpty();
        // $validate->addColumn('system_code', TaskLogValidate::$alias['system_code']);
        // $validate->addColumn('url', TaskLogValidate::$alias['url']);
        // $validate->addColumn('request_header', TaskLogValidate::$alias['request_header']);
        // $validate->addColumn('request_method', TaskLogValidate::$alias['request_method']);
        // $validate->addColumn('request_type', TaskLogValidate::$alias['request_type']);
        // $validate->addColumn('request_body', TaskLogValidate::$alias['request_body']);
        // $validate->addColumn('request_duration', TaskLogValidate::$alias['request_duration']);
        // $validate->addColumn('response_body', TaskLogValidate::$alias['response_body']);
        // $validate->addColumn('response_http_code', TaskLogValidate::$alias['response_http_code']);
        // $validate->addColumn('response_business_code', TaskLogValidate::$alias['response_business_code']);
        // $validate->addColumn('status', TaskLogValidate::$alias['status']);
        // $validate->addColumn('create_time', TaskLogValidate::$alias['create_time']);
        // $validate->addColumn('update_time', TaskLogValidate::$alias['update_time']);
        
        return $validate;
    }

    public function delete(array $params): ?Validate
    {
        $validate = new Validate();

        $validate->addColumn('id', TaskLogValidate::$alias['id'])->required()->notEmpty();
        // $validate->addColumn('system_code', TaskLogValidate::$alias['system_code']);
        // $validate->addColumn('url', TaskLogValidate::$alias['url']);
        // $validate->addColumn('request_header', TaskLogValidate::$alias['request_header']);
        // $validate->addColumn('request_method', TaskLogValidate::$alias['request_method']);
        // $validate->addColumn('request_type', TaskLogValidate::$alias['request_type']);
        // $validate->addColumn('request_body', TaskLogValidate::$alias['request_body']);
        // $validate->addColumn('request_duration', TaskLogValidate::$alias['request_duration']);
        // $validate->addColumn('response_body', TaskLogValidate::$alias['response_body']);
        // $validate->addColumn('response_http_code', TaskLogValidate::$alias['response_http_code']);
        // $validate->addColumn('response_business_code', TaskLogValidate::$alias['response_business_code']);
        // $validate->addColumn('status', TaskLogValidate::$alias['status']);
        // $validate->addColumn('create_time', TaskLogValidate::$alias['create_time']);
        // $validate->addColumn('update_time', TaskLogValidate::$alias['update_time']);
        
        return $validate;
    }
}
