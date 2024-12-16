<?php

declare(strict_types=1);

namespace BaksDev\Reference\Materials\Furniture\Messenger;

use BaksDev\Reference\Materials\Furniture\Type\Materials\MaterialsFurnitureCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class MaterialsFurnitureHandler
{
    public function __construct(
        private MaterialsFurnitureCollection $currentEvent,
    ) {}

    public function __invoke(MaterialsFurnitureHandlerMessage $message): void {}
}
