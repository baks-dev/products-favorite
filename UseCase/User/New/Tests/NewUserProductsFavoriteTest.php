<?php

namespace BaksDev\Products\Favorite\UseCase\User\New\Tests;

use BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests\CategoryProductNewTest;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteDTO;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\UseCase\Admin\NewEdit\Tests\ProductsProductNewTest;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Users\User\Tests\TestUserAccount;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteHandler;
use BaksDev\Products\Product\Type\Id\ProductUid;

/**
 * @group favorite-test
 */
class NewUserProductsFavoriteTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        ProductsProductNewTest::setUpBeforeClass();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        /** @var ProductsFavoriteDataUpdateInterface $ProductsFavoriteDataUpdate */
        $ProductsFavoriteDataUpdate = self::getContainer()->get(ProductsFavoriteDataUpdateInterface::class);

        $ProductsFavorite = $ProductsFavoriteDataUpdate
            ->user(UserUid::TEST)
            ->invariable(ProductInvariableUid::TEST)
            ->find();

        if($ProductsFavorite)
        {
            $em->remove($ProductsFavorite);
            $em->flush();
            $em->clear();
        }
    }

    public function testUseCase()
    {
        $FavoriteDTO = new UserProductsFavoriteDTO();

        $FavoriteDTO->setInvariable(ProductInvariableUid::TEST);
        $FavoriteDTO->setUsr(UserUid::TEST);

        self::assertSame(ProductInvariableUid::TEST, (string)$FavoriteDTO->getInvariable());
        self::assertSame(UserUid::TEST, (string)$FavoriteDTO->getUsr());

        /** @var AuthenticatedProductFavoriteHandler $FavoriteHandler */
        $FavoriteHandler = self::getContainer()->get(UserProductsFavoriteHandler::class);
        $handle = $FavoriteHandler->handle($FavoriteDTO);

        self::assertTrue(($handle instanceof ProductsFavorite), $handle.': Ошибка создания Favorite');
    }
}