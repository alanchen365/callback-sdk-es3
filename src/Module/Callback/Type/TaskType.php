<?php

namespace App\Module\Callback\Type;

use EasySwoole\Spl\SplBean;

class TaskType extends SplBean
{
    protected $apiCode;
    protected $requestParams;
    protected $taskCode;

    /**
     * @return mixed
     */
    public function getApiCode()
    {
        return $this->apiCode;
    }

    /**
     * @param mixed $apiCode
     */
    public function setApiCode($apiCode): void
    {
        $this->apiCode = $apiCode;
    }

    /**
     * @return mixed
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * @param mixed $requestParams
     */
    public function setRequestParams($requestParams): void
    {
        $this->requestParams = $requestParams;
    }

    /**
     * @return mixed
     */
    public function getTaskCode()
    {
        return $this->taskCode;
    }

    /**
     * @param mixed $taskCode
     */
    public function setTaskCode($taskCode): void
    {
        $this->taskCode = $taskCode;
    }
}
