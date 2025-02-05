<?php


declare(strict_types=1);

namespace BaksDev\Products\Favorite\Repository\ProductsFavoriteAll;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Product\Entity\Category\ProductCategory;
use BaksDev\Products\Product\Entity\Event\ProductEvent;
use BaksDev\Products\Product\Entity\Info\ProductInfo;
use BaksDev\Products\Product\Entity\Offers\Image\ProductOfferImage;
use BaksDev\Products\Product\Entity\Offers\Price\ProductOfferPrice;
use BaksDev\Products\Product\Entity\Offers\ProductOffer;
use BaksDev\Products\Product\Entity\Offers\Quantity\ProductOfferQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Image\ProductVariationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Image\ProductModificationImage;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Price\ProductModificationPrice;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\ProductModification;
use BaksDev\Products\Product\Entity\Offers\Variation\Modification\Quantity\ProductModificationQuantity;
use BaksDev\Products\Product\Entity\Offers\Variation\Price\ProductVariationPrice;
use BaksDev\Products\Product\Entity\Offers\Variation\ProductVariation;
use BaksDev\Products\Product\Entity\Offers\Variation\Quantity\ProductVariationQuantity;
use BaksDev\Products\Product\Entity\Photo\ProductPhoto;
use BaksDev\Products\Product\Entity\Price\ProductPrice;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\DBAL\ArrayParameterType;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\Session;

final class ProductsFavoriteAll implements ProductsFavoriteAllInterface
{
    private Session $session;

    private UserUid|false $usr = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
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

    public function session(Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    private function builder(): DBALQueryBuilder
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->leftJoin(
            'product_invariable',
            Product::class,
            'product',
            'product.id = product_invariable.product'
        );

        $dbal
            ->addSelect('product_offer.const AS product_offer_const')
            ->addSelect("product_offer.value as product_offer_value")
            ->leftJoin(
                'product_invariable',
                ProductOffer::class,
                'product_offer',
                'product_offer.event = product.event AND product_offer.const = product_invariable.offer'
            );

        $dbal
            ->addSelect('product_variation.const AS product_variation_const')
            ->addSelect("product_variation.value as product_variation_value")
            ->leftJoin(
                'product_offer',
                ProductVariation::class,
                'product_variation',
                'product_variation.offer = product_offer.id AND product_variation.const = product_invariable.variation'
            );

        $dbal
            ->addSelect('product_modification.const AS product_modification_const')
            ->addSelect("product_modification.value as product_modification_value")
            ->leftJoin(
                'product_variation',
                ProductModification::class,
                'product_modification',
                'product_modification.variation = product_variation.id AND product_modification.const = product_invariable.modification'
            );

        /**  Название */
        $dbal
            ->addSelect('product_trans.name AS product_trans_name')
            ->leftJoin(
                'product',
                ProductTrans::class,
                'product_trans',
                'product_trans.event = product.event AND product_trans.local = :local'
            );

        /** Фото */

        $dbal
            ->leftJoin(
                'product',
                ProductPhoto::class,
                'product_photo',
                'product_photo.event = product.event AND product_photo.root = TRUE'
            );

        $dbal
            ->leftJoin(
                'product_offer',
                ProductOfferImage::class,
                'product_offer_images',
                'product_offer_images.offer = product_offer.id AND product_offer_images.root = TRUE'
            );

        $dbal
            ->leftJoin(
                'product_variation',
                ProductVariationImage::class,
                'product_variation_images',
                'product_variation_images.variation = product_variation.id AND product_variation_images.root = TRUE'
            );

        $dbal
            ->leftJoin(
                'product_modification',
                ProductModificationImage::class,
                'product_modification_images',
                'product_modification_images.modification = product_modification.id AND product_modification_images.root = TRUE'
            );

        $dbal->addSelect(
            "
			CASE
			
			    WHEN product_modification_images.name IS NOT NULL 
			   THEN CONCAT ( '/upload/".$dbal->table(ProductModificationImage::class)."' , '/', product_modification_images.name)
			   
			   WHEN product_variation_images.name IS NOT NULL 
			   THEN CONCAT ( '/upload/".$dbal->table(ProductVariationImage::class)."' , '/', product_variation_images.name)
			   
			   WHEN product_offer_images.name IS NOT NULL 
			   THEN CONCAT ( '/upload/".$dbal->table(ProductOfferImage::class)."' , '/', product_offer_images.name)
			   
			   WHEN product_photo.name IS NOT NULL 
			   THEN CONCAT ( '/upload/".$dbal->table(ProductPhoto::class)."' , '/', product_photo.name)
			   
			   ELSE NULL
			END AS product_image
		"
        );
        /** Расширение */
        $dbal->addSelect("
			CASE
			   WHEN product_variation_images.name IS NOT NULL 
			   THEN product_variation_images.ext
			   
			   WHEN product_offer_images.name IS NOT NULL 
			   THEN product_offer_images.ext
			   
			   WHEN product_photo.name IS NOT NULL 
			   THEN product_photo.ext
			   
			   ELSE NULL
			END AS product_image_ext
		");

        /** Флаг загрузки файла CDN */
        $dbal->addSelect("
			CASE
			   WHEN product_variation_images.name IS NOT NULL 
			   THEN product_variation_images.cdn
					
			   WHEN product_offer_images.name IS NOT NULL 
			   THEN product_offer_images.cdn
					
			   WHEN product_photo.name IS NOT NULL 
			   THEN product_photo.cdn
			   
			   ELSE NULL
			END AS product_images_cdn
		");

        /** Цена */
        /* Базовая Цена товара */
        $dbal->leftJoin(
            'product',
            ProductPrice::class,
            'product_price',
            'product_price.event = product.event'
        );

        /* Цена торгового предложения */
        $dbal->leftJoin(
            'product_offer',
            ProductOfferPrice::class,
            'product_offer_price',
            'product_offer_price.offer = product_offer.id'
        );

        /* Цена множественного варианта */
        $dbal->leftJoin(
            'product_variation',
            ProductVariationPrice::class,
            'product_variation_price',
            'product_variation_price.variation = product_variation.id'
        );

        /* Цена модификации множественного варианта */
        $dbal->leftJoin(
            'product_modification',
            ProductModificationPrice::class,
            'product_modification_price',
            'product_modification_price.modification = product_modification.id'
        );

        /* Стоимость продукта */

        $dbal->addSelect(
            '
			CASE
			   WHEN product_modification_price.price IS NOT NULL AND product_modification_price.price > 0 
			   THEN product_modification_price.price
			   
			   WHEN product_variation_price.price IS NOT NULL AND product_variation_price.price > 0 
			   THEN product_variation_price.price
			   
			   WHEN product_offer_price.price IS NOT NULL AND product_offer_price.price > 0 
			   THEN product_offer_price.price
			   
			   WHEN product_price.price IS NOT NULL AND product_price.price > 0 
			   THEN product_price.price
			   
			   ELSE NULL
			END AS product_price
		');


        /** Наличие для добавления в корзину */
        /* Наличие и резерв торгового предложения */
        $dbal
            ->leftJoin(
            'product_offer',
            ProductOfferQuantity::class,
            'product_offer_quantity',
            'product_offer_quantity.offer = product_offer.id'
        );

        /* Наличие и резерв множественного варианта */
        $dbal
            ->leftJoin(
            'product_variation',
            ProductVariationQuantity::class,
            'product_variation_quantity',
            'product_variation_quantity.variation = product_variation.id'
        );

        $dbal

            ->leftJoin(
                'product_modification',
                ProductModificationQuantity::class,
                'product_modification_quantity',
                'product_modification_quantity.modification = product_modification.id'
            );


        $dbal->addSelect("
			COALESCE(
                NULLIF(product_modification_quantity.quantity, 0),
                NULLIF(product_variation_quantity.quantity, 0),
                NULLIF(product_offer_quantity.quantity, 0),
                NULLIF(product_price.quantity, 0),
                0
            ) AS product_quantity
		");

        $dbal->addSelect("
			COALESCE(
                NULLIF(product_modification_quantity.reserve, 0),
                NULLIF(product_variation_quantity.reserve, 0),
                NULLIF(product_offer_quantity.reserve, 0),
                NULLIF(product_price.reserve, 0),
                0
            ) AS product_reserve
		");

        $dbal
            ->addSelect("product_event.id AS product_event_id")
            ->leftJoin(
                'product',
                ProductEvent::class,
                'product_event',
                'product_event.id = product.event'
            );

        /* Категория товара */
        $dbal
            ->addSelect("product_category.category AS product_category")
            ->leftJoin(
            'product_event',
            ProductCategory::class,
            'product_category',
            'product_category.event = product_event.id'
        );

        $dbal
            ->addSelect("category.event AS category_event")
            ->leftJoin(
                'product_event',
                CategoryProduct::class,
                'category',
                'category.id = product_category.category'
            );

        $dbal
            ->addSelect('category_info.url AS category_url')
            ->leftJoin(
                'category',
                CategoryProductInfo::class,
                'category_info',
                'category_info.event = category.event AND category_info.active = true'
            );


        $dbal
            ->addSelect('product_info.url AS product_url')
            ->leftJoin(
                'product',
                ProductInfo::class,
                'product_info',
                'product_info.product = product.id'
            );


        return $dbal;
    }

    /** Метод возвращает пагинатор ProductsFavorite */
    public function findUserPaginator(): PaginatorInterface
    {
        if(false === $this->usr)
        {
            throw new InvalidArgumentException('Invalid Argument User');
        }

        $dbal = $this->builder();

        $dbal
            ->from(ProductsFavorite::class, 'favorite')
            ->where('favorite.usr = :usr')
            ->setParameter('usr', $this->usr, UserUid::TYPE)
        ;

        $dbal
            ->addSelect('product_invariable.id as product_invariable_id')
            ->addSelect('product_invariable.offer AS product_invariable_offer_const')
            ->leftJoin(
                'favorite',
                ProductInvariable::class,
                'product_invariable',
                'product_invariable.id = favorite.invariable'
            );

        return $this->paginator->fetchAllAssociative($dbal);
    }

    public function findPublicPaginator(): PaginatorInterface
    {
        $favoriteProducts = $this->session->get('favorite') ?? [];

        $dbal = $this->builder();

        $dbal
            ->addSelect('product_invariable.id as product_invariable_id')
            ->from(ProductInvariable::class, 'product_invariable')
            ->where('product_invariable.id IN (:favoriteProducts)')
            ->setParameter('favoriteProducts', array_values($favoriteProducts), ArrayParameterType::STRING);

        return $this->paginator->fetchAllAssociative($dbal);
    }
}
