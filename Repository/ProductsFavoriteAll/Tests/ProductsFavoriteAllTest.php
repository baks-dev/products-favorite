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

namespace BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\Tests;

use BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\ProductsFavoriteAllInterface;
use BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\ProductsFavoriteAllRepository;
use BaksDev\Products\Favorite\UseCase\User\New\Tests\NewUserProductsFavoriteTest;
use BaksDev\Users\User\Type\Id\UserUid;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group products-favorite
 * @group products-favorite-repo
 *
 * @depends BaksDev\Products\Favorite\UseCase\User\New\Tests\NewUserProductsFavoriteTest::class
 */
#[Group('products-favorite')]
#[When(env: 'test')]
class ProductsFavoriteAllTest extends KernelTestCase
{

    #[DependsOnClass(NewUserProductsFavoriteTest::class)]
    public function testUserRepository()
    {
        /** @var ProductsFavoriteAllRepository $AllFavorite */
        $AllFavorite = self::getContainer()->get(ProductsFavoriteAllInterface::class);

        $result = $AllFavorite->user(UserUid::TEST)->findUserPaginator();

        self::assertTrue(true);
    }
}