<?php

namespace BaksDev\Products\Favorite\Repository\ProductsFavoriteAll;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;

interface ProductsFavoriteAllInterface
{
    public function user(User|UserUid|string $usr): self;

    public function findUserPaginator(): PaginatorInterface;

    public function findPublicPaginator(): PaginatorInterface;
}