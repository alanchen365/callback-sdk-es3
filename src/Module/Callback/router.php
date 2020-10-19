<?php

use FastRoute\RouteCollector;

return [
  '/api' => [
    '/callback' => function (RouteCollector $route) {
        
        // 添加单条
        $route->addRoute(['POST'], '/task', '/Callback/Task/save');
    // 删除单条
        // $route->addRoute(['DELETE'], '/task/{id:\d+}', '/Callback/Task/delete');
        // 修改单条
        // $route->addRoute(['PUT'], '/task/{id:\d+}', '/Callback/Task/update');
        // 查看单个
        // $route->addRoute(['GET'], '/task/{id:\d+}', '/Callback/Task/get');
        // 查询列表
        // $route->addRoute(['GET'], '/task', '/Callback/Task/index');
        
        
        // 添加单条
        // $route->addRoute(['POST'], '/tasklog', '/Callback/TaskLog/save');
        // 删除单条
        // $route->addRoute(['DELETE'], '/tasklog/{id:\d+}', '/Callback/TaskLog/delete');
        // 修改单条
        // $route->addRoute(['PUT'], '/tasklog/{id:\d+}', '/Callback/TaskLog/update');
        // 查看单个
        // $route->addRoute(['GET'], '/tasklog/{id:\d+}', '/Callback/TaskLog/get');
        // 查询列表
        // $route->addRoute(['GET'], '/tasklog', '/Callback/TaskLog/index');
    },
  ]
];
