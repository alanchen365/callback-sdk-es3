<?php

namespace App\Module\Callback\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class TaskFailQueue extends Queue
{
    use Singleton;
}
