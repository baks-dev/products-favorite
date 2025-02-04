<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Controller\Public;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteHandler;
use BaksDev\Products\Favorite\UseCase\Public\New\PublicProductsFavoriteForm;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteForm;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;


#[AsController]
final class NewController extends AbstractController
{
    private $str;
    private $addFlash;

    #[Route('/favorite/new/{invariable}', name: 'newedit.new', methods: ['POST'])]
    public function news(
        Request $request,
        AnonymousProductsFavoriteHandler $AnonymousProductsFavoriteHandler,
        UserProductsFavoriteHandler $UserProductsFavoriteHandler,
        FormFactoryInterface $formFactory,
        string|null $invariable = null,
    ): Response
    {
        $ProductsFavoriteDTO = $this->getUsr() ? new UserProductsFavoriteDTO() : new AnonymousProductsFavoriteDTO();

        if($this->getUsr())
        {
            $ProductsFavoriteDTO->setUsr($this->getUsr());
        }
        if($invariable !== null) {
            $ProductsFavoriteDTO->setInvariable($invariable);
        }

        $formName = $invariable ?? 'public_products_favorite_form';

        $form = $formFactory
            ->createNamed(
                $formName,
                PublicProductsFavoriteForm::class,
                $ProductsFavoriteDTO
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('products_favorite'))
        {
            $handle = $this->getUsr() ? $UserProductsFavoriteHandler->handle($ProductsFavoriteDTO) : $AnonymousProductsFavoriteHandler->handle($ProductsFavoriteDTO);

            if($handle instanceof ProductsFavorite || $this->getUsr() === null)
            {
                return $request->isXmlHttpRequest() === true ? new JsonResponse(['success' => true]) : $this->redirectToRoute('products-favorite:public.index');
            }
        }

        if($request->isXmlHttpRequest() === true)
        {
            return new JsonResponse(['success' => false]);
        }

        $this->str = 'text';
        $this->addFlash = $this->addFlash(
            'danger',
            'delete.message',
            'favorite'
        );
        $this->addFlash;

        return $this->redirectToRoute('products-favorite:public.index');
    }
}
