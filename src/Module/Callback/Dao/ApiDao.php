<?php

namespace App\Module\Callback\Dao;

use App\Module\Callback\Model\ApiModel;

class ApiDao extends BaseCallbackDao
{
    public function __construct()
    {
        $this->setModel(new ApiModel());
    }

    /**
     * model
     * @return ApiModel
     */
    public function getModel(): ApiModel
    {
        return $this->model;
    }
}
