<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Listeners;

use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;


#[AsEventListener(event: AuthenticationSuccessEvent::class, method: 'onAuthEvent')]
final class AddFavoriteFromSessionListener
{
    public function __construct(
        private readonly RequestStack $request,
        private readonly ProductsFavoriteDataUpdateInterface $repository,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
    ) {}

    public function onAuthEvent(AuthenticationSuccessEvent $event)
    {
        $usr = $event->getAuthenticationToken()->getUser()->getId();
        $invariables = $this->request->getSession()->get('favorite');
        if (empty($invariables)) {
            return;
        }

        foreach($invariables as $invariable) {
            $favorite = $this->repository->invariable($invariable)->user($usr)->find();

            if ($favorite) {
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
    }
}