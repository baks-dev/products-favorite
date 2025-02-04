<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Entity;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/* ProductsFavorite */

#[ORM\Entity]
#[ORM\Table(name: 'products_favorite')]
class ProductsFavorite extends EntityState
{
    /** Идентификатор Invariable */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductInvariableUid::TYPE)]
    private ProductInvariableUid $invariable;

    /** Идентификатор пользователя */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: UserUid::TYPE)]
    private UserUid $usr;

    public function __toString(): string
    {
        return (string) $this->usr;
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


    public function getDto($dto): mixed
    {
        if($dto instanceof ProductsFavoriteInterface)
        {
            return parent::getDto($dto);
        }

        throw new \InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductsFavoriteInterface)
        {
            return parent::setEntity($dto);
        }

        throw new \InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}