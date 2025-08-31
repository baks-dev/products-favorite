<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Controller\Public\Tests;

use BaksDev\Users\User\Tests\TestUserAccount;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('products-favorite')]
#[When(env: 'test')]
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