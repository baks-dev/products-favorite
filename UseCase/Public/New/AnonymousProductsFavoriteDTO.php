<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\UseCase\Public\New;

use BaksDev\Products\Favorite\Entity\ProductsFavoriteInterface;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use Symfony\Component\Validator\Constraints as Assert;


final class AnonymousProductsFavoriteDTO implements ProductsFavoriteInterface
{
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private ProductInvariableUid $invariable;

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
}