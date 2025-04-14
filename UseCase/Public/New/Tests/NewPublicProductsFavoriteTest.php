<?php

namespace BaksDev\Products\Favorite\UseCase\Public\New\Tests;

use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\Public\New\PublicProductsFavoriteForm;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Forms;

/**
 * @group products-favorite
 */
class NewPublicProductsFavoriteTest extends WebTestCase
{
    private const string URL = '/favorite/new';

    public static function testUseCase(): void
    {
        $client = static::createClient();

        $DTO = new AnonymousProductsFavoriteDTO();
        $DTO->setInvariable(ProductInvariableUid::TEST);

        $form = Forms::createFormFactory()->create(PublicProductsFavoriteForm::class, $DTO);
        $client->request('POST', self::URL, [ $form->getName() =>
            ['invariable' => ProductInvariableUid::TEST]
        ]);

        self::assertResponseRedirects();

        $session = $client->getRequest()->getSession();
        $favoriteProducts = $session->get('favorite') ?? [];
        self::assertNotEmpty($favoriteProducts);
        self::assertTrue(in_array(ProductInvariableUid::TEST, $favoriteProducts));
    }
}