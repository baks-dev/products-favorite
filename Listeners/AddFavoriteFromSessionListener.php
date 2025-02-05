<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Listeners;

use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[AsEventListener(event: AuthenticationSuccessEvent::class, method: 'onAuthEvent')]
final class AddFavoriteFromSessionListener
{
    public function __construct(
        private readonly RequestStack $request,
        private readonly ProductsFavoriteDataUpdateInterface $ProductsFavoriteDataUpdate,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     *  Сохраняем в БД избранное из сессии, если пользователь добавлял как гость
     */
    public function onAuthEvent(AuthenticationSuccessEvent $event): string|bool
    {
        $usr = $event->getAuthenticationToken()->getUser()?->getId();

        if(is_null($usr))
        {
            return false;
        }

        $invariables = $this->request->getSession()->get('favorite');

        if(empty($invariables))
        {
            return false;
        }

        /**
         * Если пользователь авторизован - сохраняем все идентификаторы Invariable
         */

        foreach($invariables as $invariable)
        {
            $favorite = $this->ProductsFavoriteDataUpdate
                ->invariable($invariable)
                ->user($usr)
                ->find();

            if(false === $favorite)
            {
                continue;
            }

            $ProductsFavorite = new ProductsFavorite();

            $ProductsFavorite
                ->setUsr($usr)
                ->setInvariable($invariable);

            $errors = $this->validator->validate($ProductsFavorite);

            if(count($errors) > 0)
            {
                return (string) $errors;
            }

            $this->entityManager->persist($ProductsFavorite);
            $this->entityManager->flush();
        }

        return true;
    }
}