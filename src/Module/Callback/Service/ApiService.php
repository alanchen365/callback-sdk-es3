<?php

namespace App\Module\Callback\Service;

use App\Module\Callback\Dao\ApiDao;

class ApiService extends BaseCallbackService
{
    public function __construct()
    {
        $this->setDao(new ApiDao());
    }

    /**
     * dao
     * @return ApiDao
     */
    public function getDao(): ApiDao
    {
        return $this->dao;
    }
}
