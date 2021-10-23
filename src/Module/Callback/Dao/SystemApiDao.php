<?php

namespace App\Module\Callback\Dao;

use App\Constant\ResultConst;
use App\Module\Callback\Model\SystemApiModel;

class SystemApiDao extends BaseCallbackDao
{
    public function __construct()
    {
        $this->setModel(new SystemApiModel());
    }

    public function pushList(string $apiCode): array
    {
        $env = strtoupper(env());
        $sql = "SELECT
                system.id system_id,
                system.system_name,
                system.request_header,
                system.system_code,
                system.domain,
                system.response_key_code,
                system.response_key_msg,
                system.response_success_condition,
                system.response_success_value,
                api.api_code,
                api.api_name,
                api.path,
                api.is_async,
                api.request_method,
                api.request_type
                from `callback_system_api`  system_api
                left join `callback_api` api
                on system_api.`api_code` = api.`api_code`
                left join `callback_system` system
                on system_api.system_code = system.system_code
                where api.delete_flg =0 and system.delete_flg = 0
              and api.api_code = '$apiCode' 
              and system.env = '$env'
              ";

        $list = $this->query($sql);
        return $list;
    }

    /**
     * model
     * @return SystemApiModel
     */
    public function getModel(): SystemApiModel
    {
        return $this->model;
    }
}
