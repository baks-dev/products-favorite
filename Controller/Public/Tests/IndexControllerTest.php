<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Controller\Public\Tests;

use BaksDev\Users\User\Tests\TestUserAccount;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group products-favorite
 */
final class IndexControllerTest extends WebTestCase
{
    private const URL = '/favorites';

    /** Доступ по без роли */
    public function testGuestFiled(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->request('GET', self::URL);

            // Full authentication is required to access this resource
            self::assertResponseStatusCodeSame(200);
        }

        self::assertTrue(true);
    }
}