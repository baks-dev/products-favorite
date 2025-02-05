<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\UseCase\User\New;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Core\Validator\ValidatorCollectionInterface;
use BaksDev\Orders\Order\Messenger\OrderMessage;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\Messenger\ProductsFavoriteMessage;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use BaksDev\Users\Profile\Group\Messenger\ProfileGroupMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserProductsFavoriteHandler// extends AbstractHandler
{
    public function __construct(
        private ProductsFavoriteDataUpdateInterface $ProductsFavoriteRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private LoggerInterface $logger,
        private MessageDispatchInterface $messageDispatch
    ) {}

    /** @see ProductsFavorite */
    public function handle(UserProductsFavoriteDTO $command): string|ProductsFavorite|array
    {
        $ProductsFavorite = $this->ProductsFavoriteRepository
            ->user($command->getUsr())
            ->invariable($command->getInvariable())
            ->find();

        if($ProductsFavorite)
        {
            $this->entityManager->remove($ProductsFavorite);
            $this->entityManager->flush();

            $this
                ->messageDispatch->addClearCacheOther('products-favorite')
                ->dispatch(
                    message: new ProductsFavoriteMessage(),
                    transport: 'products-favorite'
                );

            return $ProductsFavorite;
        }

        $ProductsFavorite = new ProductsFavorite();

        $ProductsFavorite
            ->setUsr($command->getUsr())
            ->setInvariable($command->getInvariable());

        $errors = $this->validator->validate($ProductsFavorite);

        if(count($errors) > 0)
        {
            return (string) $errors;
        }

        $this->entityManager->persist($ProductsFavorite);
        $this->entityManager->flush();

        $this
            ->messageDispatch->addClearCacheOther('products-favorite')
            ->dispatch(
                message: new ProductsFavoriteMessage(),
                transport: 'products-favorite'
            );

        return $ProductsFavorite;
    }
}