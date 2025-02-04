<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Favorite\BaksDevProductsFavoriteBundle;
use Symfony\Config\DoctrineConfig;


return static function (DoctrineConfig $doctrine): void {

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault
        ->mapping('products-favorite')
        ->type('attribute')
        ->dir(BaksDevProductsFavoriteBundle::PATH . 'Entity')
        ->isBundle(false)
        ->prefix(BaksDevProductsFavoriteBundle::NAMESPACE . '\\Entity')
        ->alias('products-favorite');
};