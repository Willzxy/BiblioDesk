<?php

namespace App;

require_once '../MinhaFramework/Init/Bootstrap.php';

use MF\BootStrap;

class Route extends BootStrap
{
    function InitRoutes()
    {
        $routes['endpoint'] = array(
            'route' => '/teste',
            'controller' => 'TesteController',
            'action' => 'index',
            'middleware' => 'TesteMiddleware'
        );

        $this->setRoutes($routes);
    }
}
