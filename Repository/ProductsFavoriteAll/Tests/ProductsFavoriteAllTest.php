<?php

namespace BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\Tests;

use BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\ProductsFavoriteAll;
use BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\ProductsFavoriteAllInterface;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group favorite-test
 * @group favorite-test-rep
 */
class ProductsFavoriteAllTest extends KernelTestCase
{
    public function testUserRepository()
    {
        /** @var ProductsFavoriteAll $AllFavorite */
        $AllFavorite = self::getContainer()->get(ProductsFavoriteAllInterface::class);
        $paginator = $AllFavorite->user(UserUid::TEST)->findUserPaginator();

        $data = $paginator->getData();

        if(empty($data))
        {
            self::assertTrue(true);
            return;
        }

        $array_keys = [
            "product_offer_const",
            "product_variation_const",
            "product_modification_const",
            "product_trans_name",
            "product_image",
            "product_image_ext",
            "product_images_cdn",
            "product_price",
            "product_quantity",
            "product_reserve",
            "product_invariable_offer_const",
            "product_invariable_id",
            "product_event_id",
            "product_category",
            "category_event",
            "category_url",
            "product_url",
            "product_offer_value",
            "product_variation_value",
            "product_modification_value",
        ];

        $current = current($data);

        foreach($current as $key => $value)
        {
            self::assertTrue(in_array($key, $array_keys), sprintf('Появился новый ключ %s', $key));
        }

        foreach($array_keys as $key)
        {
            self::assertTrue(array_key_exists($key, $current));
        }
    }
}