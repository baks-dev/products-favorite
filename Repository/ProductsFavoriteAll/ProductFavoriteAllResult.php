<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 */

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Repository\ProductsFavoriteAll;

use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Products\Product\Repository\Cards\ProductCardResultInterface;
use BaksDev\Products\Product\Type\Event\ProductEventUid;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Product\Type\Offers\ConstId\ProductOfferConst;
use BaksDev\Products\Product\Type\Offers\Id\ProductOfferUid;
use BaksDev\Products\Product\Type\Offers\Variation\ConstId\ProductVariationConst;
use BaksDev\Products\Product\Type\Offers\Variation\Id\ProductVariationUid;
use BaksDev\Products\Product\Type\Offers\Variation\Modification\ConstId\ProductModificationConst;
use BaksDev\Products\Product\Type\Offers\Variation\Modification\Id\ProductModificationUid;
use BaksDev\Reference\Currency\Type\Currency;
use BaksDev\Reference\Money\Type\Money;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** @see ProductsFavoriteAllRepository */
#[Exclude]
final readonly class ProductFavoriteAllResult implements ProductCardResultInterface
{
    public function __construct(
        private string $product_id,
        private string $product_event,
        private string|null $product_offer_id,
        private string|null $product_offer_const,
        private string|null $product_offer_value,
        private string|null $product_offer_postfix,
        private string|null $product_offer_reference,
        private string|null $product_variation_id,
        private string|null $product_variation_const,
        private string|null $product_variation_value,
        private string|null $product_variation_postfix,
        private string|null $product_variation_reference,
        private string|null $product_modification_id,
        private string|null $product_modification_const,
        private string|null $product_modification_value,
        private string|null $product_modification_postfix,
        private string|null $product_modification_reference,
        private string $product_trans_name,
        private string $product_images,
        private string|null $product_invariable_offer_const,
        private string|null $product_invariable_id,
        private string $product_category,
        private string $category_event,
        private string|null $category_url,
        private string $product_url,
        private int|null $product_price,
        private int|null $product_quantity,
        private int|null $product_reserve,
        private int|null $product_old_price,
        private string|null $product_currency,

        private string|null $profile_discount = null,

    ) {}

    public function getProductId(): ProductUid
    {
        return new ProductUid($this->product_id);
    }

    public function getProductEvent(): ProductEventUid
    {
        return new ProductEventUid($this->product_event);
    }

    public function getProductOfferUid(): ProductOfferUid|null
    {
        if(null === $this->product_offer_id)
        {
            return null;
        }

        return new ProductOfferUid($this->product_offer_id);
    }

    public function getProductOfferConst(): ProductOfferConst|null
    {
        if(null === $this->product_offer_const)
        {
            return null;
        }

        return new ProductOfferConst($this->product_offer_const);
    }

    public function getProductOfferValue(): ?string
    {
        return $this->product_offer_value;
    }

    public function getProductOfferPostfix(): ?string
    {
        return $this->product_offer_postfix;
    }

    public function getProductOfferReference(): ?string
    {
        return $this->product_offer_reference;
    }

    public function getProductVariationUid(): ProductVariationUid|null
    {
        if(null === $this->product_variation_id)
        {
            return null;
        }

        return new ProductVariationUid($this->product_variation_id);
    }

    public function getProductVariationConst(): ProductVariationConst|null
    {
        if(null === $this->product_variation_const)
        {
            return null;
        }

        return new ProductVariationConst($this->product_variation_const);
    }

    public function getProductVariationValue(): ?string
    {
        return $this->product_variation_value;
    }

    public function getProductVariationPostfix(): ?string
    {
        return $this->product_variation_postfix;
    }

    public function getProductVariationReference(): ?string
    {
        return $this->product_variation_reference;
    }

    public function getProductModificationUid(): ProductModificationUid|null
    {
        if(null === $this->product_modification_id)
        {
            return null;
        }

        return new ProductModificationUid($this->product_modification_id);
    }

    public function getProductModificationConst(): ProductModificationConst|null
    {
        if(null === $this->product_modification_const)
        {
            return null;
        }

        return new ProductModificationConst($this->product_modification_const);
    }

    public function getProductModificationValue(): ?string
    {
        return $this->product_modification_value;
    }

    public function getProductModificationPostfix(): ?string
    {
        return $this->product_modification_postfix;
    }

    public function getProductModificationReference(): ?string
    {
        return $this->product_modification_reference;
    }

    public function getProductName(): string
    {
        return $this->product_trans_name;
    }

    public function getProductImages(): array|null
    {
        if(is_null($this->product_images))
        {
            return null;
        }

        if(false === json_validate($this->product_images))
        {
            return null;
        }

        $images = json_decode($this->product_images, true, 512, JSON_THROW_ON_ERROR);

        if(null === current($images))
        {
            return null;
        }

        return $images;
    }

    public function getProductInvariableOfferConst(): ProductOfferConst|null
    {
        if(null === $this->product_invariable_offer_const)
        {
            return null;
        }

        return new ProductOfferConst($this->product_invariable_offer_const);
    }

    public function getProductInvariableId(): ProductInvariableUid|string|null
    {
        if(null === $this->product_invariable_id)
        {
            return null;
        }

        return new ProductInvariableUid($this->product_invariable_id);
    }

    public function getProductCategory(): string
    {
        return $this->product_category;
    }

    public function getCategoryEvent(): CategoryProductEventUid
    {
        return new CategoryProductEventUid($this->category_event);
    }

    public function getCategoryUrl(): ?string
    {
        return $this->category_url;
    }

    public function getProductUrl(): string
    {
        return $this->product_url;
    }

    public function getProductPrice(): Money
    {
        $price = new Money($this->product_price, true);

        // применяем скидку пользователя из профиля
        if(false === empty($this->profile_discount))
        {
            $price->applyString($this->profile_discount);
        }

        return $price;
    }

    public function getProductOldPrice(): Money
    {
        $price = new Money($this->product_old_price, true);

        // применяем скидку пользователя из профиля
        if(false === empty($this->profile_discount))
        {
            $price->applyString($this->profile_discount);
        }

        return $price;
    }

    public function getProductQuantity(): int|null
    {
        return $this->product_quantity;
    }

    public function getProductReserve(): int|null
    {
        return $this->product_reserve;
    }

    public function getProductCurrency(): Currency
    {
        return new Currency($this->product_currency);
    }

    public function getProfileDiscount(): ?int
    {
        return $this->profile_discount;
    }

    /** Методы - заглушки */

    public function getProductOfferName(): bool
    {
        return false;
    }

    public function getProductVariationName(): bool
    {
        return false;
    }

    public function getProductModificationName(): bool
    {
        return false;
    }

    public function getProductActiveFrom(): bool
    {
        return false;
    }

    public function getProductArticle(): bool
    {
        return false;
    }

    public function getCategoryName(): bool
    {
        return false;
    }

    public function getCategorySectionField(): bool
    {
        return false;
    }

}


