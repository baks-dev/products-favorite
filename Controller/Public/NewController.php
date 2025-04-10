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

declare(strict_types=1);

namespace BaksDev\Products\Favorite\Controller\Public;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteHandler;
use BaksDev\Products\Favorite\UseCase\Public\New\PublicProductsFavoriteForm;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteDTO;
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
    /** Избранное */
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

        if($invariable !== null)
        {
            $ProductsFavoriteDTO->setInvariable($invariable);
        }

        /**
         * Если аргумент контроллера передается идентификатор UUid - присваиваем Name форме для маппинга данных из Request
         */
        $formName = $invariable ?? 'public_products_favorite_form';

        $form = $formFactory
            ->createNamed(
                name: $formName,
                type: PublicProductsFavoriteForm::class,
                data: $ProductsFavoriteDTO
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

        $this->addFlash(
            'danger',
            'delete.message',
            'favorite'
        );

        return $this->redirectToRoute('products-favorite:public.index');
    }
}
