<?php

namespace Es3\Callback;

use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Utility\File;

class CallbackSdk
{
    use Singleton;

    public function run()
    {
        /** 先删除 */
        $targetModule = EASYSWOOLE_ROOT . '/App/Module/Callback/';
        File::deleteDir($targetModule);

        /** 后复制 */
        $sdkModule = EASYSWOOLE_ROOT . '/vendor/alanchen365/callback-sdk-es3/src/Module/Callback/';
        File::copyDir($sdkModule, $targetModule);
    }
}
