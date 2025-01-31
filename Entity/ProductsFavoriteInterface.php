<?php

namespace BaksDev\Products\Favorite\Entity;

use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;

interface ProductsFavoriteInterface
{
    public function setInvariable(ProductInvariableUid|ProductInvariable|string $invariable): self;
}