<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Favorite\BaksDevProductsFavoriteBundle;

return static function(ContainerConfigurator $configurator): void {

    $services = $configurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $NAMESPACE = BaksDevProductsFavoriteBundle::NAMESPACE;
    $PATH = BaksDevProductsFavoriteBundle::PATH;

    $services->load($NAMESPACE, $PATH)
        ->exclude([
            $PATH.'{Entity,Resources,Type}',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*Message.php',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*Result.php',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*DTO.php',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*Test.php',
        ]);

};