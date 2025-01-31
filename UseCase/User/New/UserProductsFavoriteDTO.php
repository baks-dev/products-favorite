<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\UseCase\User\New;

use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Favorite\Entity\ProductsFavoriteInterface;

/** @see AuthenticatedProductFavoriteEvent */
final class UserProductsFavoriteDTO implements ProductsFavoriteInterface
{
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private ProductInvariableUid $invariable;

    #[Assert\Uuid]
    #[Assert\NotBlank]
    private ?UserUid $usr = null;

    public function getUsr(): ?UserUid
    {
        return $this->usr;
    }

    public function getInvariable(): ProductInvariableUid
    {
        return $this->invariable;
    }

    public function setInvariable(ProductInvariableUid|ProductInvariable|string $invariable): self
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

    public function setUsr(UserUid|User|string $usr): self
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

}