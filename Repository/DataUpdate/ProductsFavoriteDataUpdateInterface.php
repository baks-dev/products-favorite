<?php

namespace BaksDev\Products\Favorite\Repository\DataUpdate;

use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;

interface ProductsFavoriteDataUpdateInterface
{
    public function user(User|UserUid|string $usr): self;

    public function invariable(ProductInvariable|ProductInvariableUid|string $invariable): self;
}
