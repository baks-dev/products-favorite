<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Repository\DataUpdate;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;

/**
 * @group products-favorite-test
 */
final class ProductsFavoriteDataUpdateRepository implements ProductsFavoriteDataUpdateInterface
{
    private UserUid|false $usr = false;

    private ProductInvariableUid|false $invariable = false;

    public function __construct(
        private readonly ORMQueryBuilder $ORMQueryBuilder,
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

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

    public function find(): ProductsFavorite|null
    {
        if($this->usr === false || $this->invariable === false)
        {
            throw new \InvalidArgumentException('Invalid Argument User Or Invariable');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->select('favorite')
            ->from(ProductsFavorite::class, 'favorite');

        $orm
            ->where('favorite.usr = :usr')
            ->setParameter('usr', $this->usr, UserUid::TYPE);

        $orm
            ->andWhere('favorite.invariable = :invariable')
            ->setParameter('invariable', $this->invariable, ProductInvariableUid::TYPE);

        return $orm->getQuery()->getOneOrNullResult();
    }
    
    public function exists(): bool
    {
        if($this->usr === false || $this->invariable === false)
        {
            throw new \InvalidArgumentException('Invalid Argument User Or Invariable');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('favorite')
            ->from(ProductsFavorite::class, 'favorite');

        $dbal
            ->where('favorite.usr = :usr')
            ->setParameter('usr', $this->usr, UserUid::TYPE);

        $dbal
            ->andWhere('favorite.invariable = :invariable')
            ->setParameter('invariable', $this->invariable, ProductInvariableUid::TYPE);

        return $dbal->fetchExist();
    }

}