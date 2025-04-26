<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Repository\DataUpdate;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use InvalidArgumentException;

final class ProductsFavoriteDataUpdateRepository implements ProductsFavoriteDataUpdateInterface
{
    private UserUid|false $usr = false;

    private ProductInvariableUid|false $invariable = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function user(User|UserUid|string $usr): self
    {
        if(is_string($usr))
        {
            $usr = new UserUid($usr);
        }

        if($usr instanceof User)
        {
            $usr = $usr->getId();
        }

        $this->usr = $usr;

        return $this;
    }

    public function invariable(ProductInvariable|ProductInvariableUid|string $invariable): self
    {
        if(is_string($invariable))
        {
            $invariable = new ProductInvariableUid($invariable);
        }

        if($invariable instanceof ProductInvariable)
        {
            $invariable = $invariable->getId();
        }

        $this->invariable = $invariable;

        return $this;
    }

    public function find(): ProductsFavorite|false
    {
        if($this->usr === false || $this->invariable === false)
        {
            throw new InvalidArgumentException('Invalid Argument User Or Invariable');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->select('favorite')
            ->from(ProductsFavorite::class, 'favorite');

        $orm
            ->where('favorite.usr = :usr')
            ->setParameter(
                key: 'usr',
                value: $this->usr,
                type: UserUid::TYPE
            );

        $orm
            ->andWhere('favorite.invariable = :invariable')
            ->setParameter(
                key: 'invariable',
                value: $this->invariable,
                type: ProductInvariableUid::TYPE
            );

        return $orm->getOneOrNullResult() ?: false;
    }

}