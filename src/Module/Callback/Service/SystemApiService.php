<?php

namespace App\Module\Callback\Service;

use App\Module\Callback\Dao\SystemApiDao;

class SystemApiService extends BaseCallbackService
{
    public function __construct()
    {
        $this->setDao(new SystemApiDao());
    }

    public function pushList(string $apiCode): array
    {
        return $this->getDao()->pushList($apiCode);
    }

    /**
     * dao
     * @return SystemApiDao
     */
    public function getDao(): SystemApiDao
    {
        return $this->dao;
    }
}
