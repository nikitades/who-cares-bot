<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import('../src/App/Controller/', 'annotation');
    $routes->import('../src/Infrastructure/Symfony/Kernel.php', 'annotation');
};
