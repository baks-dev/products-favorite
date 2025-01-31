<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Favorite\BaksDevProductsFavoriteBundle;
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {

    $twig->path(
        BaksDevProductsFavoriteBundle::PATH . implode(DIRECTORY_SEPARATOR, ['Resources', 'view', '']),
        'products-favorite'
    );
};