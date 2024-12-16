<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Reference\Materials\Furniture\Type\MaterialsFurniture;
use BaksDev\Reference\Materials\Furniture\Type\MaterialsFurnitureType;
use Symfony\Config\DoctrineConfig;

return static function(DoctrineConfig $doctrine) {
    $doctrine->dbal()->type(MaterialsFurniture::TYPE)->class(MaterialsFurnitureType::class);
};
