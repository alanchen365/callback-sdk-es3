<?php

namespace App\Module\Callback\Model;

use Es3\Base\Model;

class SystemApiModel extends Model
{
    /** 数据库表名 */
    protected $tableName = 'callback_system_api';

    protected $autoTimeStamp = 'datetime';

    protected $createTime = 'create_time';
        
    protected $updateTime = 'update_time';
}
