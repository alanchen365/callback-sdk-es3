<?php

namespace App\Module\Callback\Dao;

use App\Module\Callback\Model\SystemModel;

class SystemDao extends BaseCallbackDao
{
    public function __construct()
    {
        $this->setModel(new SystemModel());
    }

    /**
     * model
     * @return SystemModel
     */
    public function getModel(): SystemModel
    {
        return $this->model;
    }
}
