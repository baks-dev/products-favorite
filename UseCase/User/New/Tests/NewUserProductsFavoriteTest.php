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

namespace BaksDev\Products\Favorite\UseCase\User\New\Tests;

use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteHandler;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Product\UseCase\Admin\NewEdit\Tests\ProductsProductNewTest;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group products-favorite
 */
class NewUserProductsFavoriteTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** Создаем тестовый продукт */
        ProductsProductNewTest::setUpBeforeClass();
        new ProductsProductNewTest()->testUseCase();

        self::ensureKernelShutdown();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $testInvariable = $em
            ->getRepository(ProductInvariable::class)
            ->find(ProductInvariableUid::TEST);

        self::assertInstanceOf(ProductInvariable::class, $testInvariable, 'ProductInvariable с тестовым ID не найден');

        /** @var ProductsFavoriteDataUpdateInterface $ProductsFavoriteDataUpdate */
        $ProductsFavoriteDataUpdate = self::getContainer()->get(ProductsFavoriteDataUpdateInterface::class);

        $ProductsFavorite = $ProductsFavoriteDataUpdate
            ->user(UserUid::TEST)
            ->invariable(ProductInvariableUid::TEST)
            ->find();

        if($ProductsFavorite instanceof ProductsFavorite)
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

        self::assertTrue($FavoriteDTO->getInvariable()->equals(ProductInvariableUid::TEST));
        self::assertTrue($FavoriteDTO->getUsr()->equals(UserUid::TEST));

        /** @var UserProductsFavoriteHandler $FavoriteHandler */
        $FavoriteHandler = self::getContainer()->get(UserProductsFavoriteHandler::class);
        $handle = $FavoriteHandler->handle($FavoriteDTO);

        self::assertTrue(($handle instanceof ProductsFavorite), $handle.': Ошибка создания Favorite');
    }
}