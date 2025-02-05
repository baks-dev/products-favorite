<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\UseCase\Public\New;

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

final class AnonymousProductsFavoriteHandler
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    public function handle(AnonymousProductsFavoriteDTO $dto): bool
    {
        try
        {
            $session = $this->requestStack->getSession();
        }
        catch(SessionNotFoundException)
        {
            return false;
        }

        $favoriteProducts = $session->get('favorite');

        if($favoriteProducts)
        {
            /**
             * Если идентификатор не найден - добавляем Invariable
             */
            if(!isset($favoriteProducts[(string) $dto->getInvariable()]))
            {
                $favoriteProducts = [(string) $dto->getInvariable() => (string) $dto->getInvariable()] + $favoriteProducts;
                $session->set('favorite', $favoriteProducts);

                return true;
            }

            /**
             * Удаляем идентификатор из массива добавленного ранее Invariable
             */
            unset($favoriteProducts[(string) $dto->getInvariable()]);
            $session->set('favorite', $favoriteProducts);

            return true;
        }

        /**
         * Создаем новый массив избранного
         */
        $favoriteProducts = [(string) $dto->getInvariable() => (string) $dto->getInvariable()];
        $session->set('favorite', $favoriteProducts);

        return true;
    }
}