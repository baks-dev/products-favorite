<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\UseCase\Public\New;

use BaksDev\Products\Favorite\Messenger\ProductsFavoriteMessage;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

final class AnonymousProductsFavoriteHandler
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    public function handle(AnonymousProductsFavoriteDTO $dto): void
    {
        try
        {
            $session = $this->requestStack->getSession();
        }
        catch(SessionNotFoundException)
        {
            return;
        }

        $favoriteProducts = $session->get('favorite');
        if($favoriteProducts)
        {
            if (!isset($favoriteProducts[(string)$dto->getInvariable()])) {
                $favoriteProducts = [(string) $dto->getInvariable() => (string) $dto->getInvariable()] + $favoriteProducts;
                $session->set('favorite', $favoriteProducts);

                return;
            }
            unset($favoriteProducts[(string)$dto->getInvariable()]);
            $session->set('favorite', $favoriteProducts);

            return;
        }

        $favoriteProducts = [(string) $dto->getInvariable() => (string) $dto->getInvariable()];
        $session->set('favorite', $favoriteProducts);
    }
}