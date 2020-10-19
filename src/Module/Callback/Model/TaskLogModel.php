<?php

namespace App\Module\Callback\Model;

use Es3\Base\Model;

class TaskLogModel extends Model
{
    /** 数据库表名 */
    protected $tableName = 'callback_task_log';

    protected $autoTimeStamp = 'datetime';

    protected $createTime = 'create_time';
        
    protected $updateTime = 'update_time';
}
