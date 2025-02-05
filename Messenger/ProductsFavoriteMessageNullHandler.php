<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Messenger;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ProductsFavoriteMessageNullHandler
{
    public function __invoke(ProductsFavoriteMessage $message): void {}
}