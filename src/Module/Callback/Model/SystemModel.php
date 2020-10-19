<?php

namespace App\Module\Callback\Model;

use Es3\Base\Model;

class SystemModel extends Model
{
    /** 数据库表名 */
    protected $tableName = 'callback_system';

    protected $autoTimeStamp = 'datetime';

    protected $createTime = 'create_time';
        
    protected $updateTime = 'update_time';
}
