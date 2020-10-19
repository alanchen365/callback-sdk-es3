<?php

namespace App\Module\Callback\Service;

use App\Module\Callback\Dao\SystemDao;

class SystemService extends BaseCallbackService
{
    public function __construct()
    {
        $this->setDao(new SystemDao());
    }

    /**
     * dao
     * @return SystemDao
     */
    public function getDao(): SystemDao
    {
        return $this->dao;
    }
}
