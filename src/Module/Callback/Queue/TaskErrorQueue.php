<?php

namespace App\Module\Callback\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class TaskErrorQueue extends Queue
{
    use Singleton;
}
