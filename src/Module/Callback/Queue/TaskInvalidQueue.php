<?php

namespace App\Module\Callback\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class TaskInvalidQueue extends Queue
{
    use Singleton;
}
