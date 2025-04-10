<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Messenger;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class ProductsFavoriteMessage
{
    public function __construct()
    {}
}