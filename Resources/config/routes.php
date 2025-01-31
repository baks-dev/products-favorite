<?php

use BaksDev\Products\Favorite\BaksDevProductsFavoriteBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;


return function (RoutingConfigurator $routes) {

    $PATH = BaksDevProductsFavoriteBundle::PATH;

    $routes->import(
        $PATH . 'Controller',
        'attribute',
        false,
        $PATH . implode(DIRECTORY_SEPARATOR, ['Controller', '**', '*Test.php'])
    )
        ->prefix(\BaksDev\Core\Type\Locale\Locale::routes())
        ->namePrefix('products-favorite:');
};