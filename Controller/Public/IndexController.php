<?php


declare(strict_types=1);

namespace BaksDev\Products\Favorite\Controller\Public;


use BaksDev\Core\Controller\AbstractController;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use BaksDev\Products\Favorite\Repository\ProductsFavoriteAll\ProductsFavoriteAllInterface;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\Public\New\PublicProductsFavoriteForm;
use BaksDev\Products\Favorite\UseCase\User\New\UserProductsFavoriteHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class IndexController extends AbstractController
{
    #[Route('/favorites/{page<\d+>}', name: 'public.index', methods: ['GET'])]
    public function index(
        Request $request,
        ProductsFavoriteAllInterface $allProductsFavorite,
        ProductsFavoriteDataUpdateInterface $favoriteDataUpdateRepository,
        UserProductsFavoriteHandler $userProductsFavoriteHandler,
        FormFactoryInterface $formFactory,
        int $page = 0
    ): Response
    {
        // Получаем список
        $User = $this->getUsr();

        if($User === null)
        {
            $session = $request->getSession();
            $query = $allProductsFavorite->session($session)->findPublicPaginator();
        }
        else
        {
            $query = $allProductsFavorite->user($User)->findUserPaginator();
        }

        $forms = [];

        foreach($query->getData() as $product)
        {
            $ProductsFavoriteDTO = new AnonymousProductsFavoriteDTO()
                ->setInvariable($product['product_invariable_id']);

            $favoriteForm = $formFactory
                ->createNamed(
                    name: $product['product_invariable_id'],
                    type: PublicProductsFavoriteForm::class,
                    data: $ProductsFavoriteDTO,
                    options: ['action' => $this->generateUrl('products-favorite:newedit.new', ['invariable' => $product['product_invariable_id']])]
                );

            $forms[$product['product_invariable_id']] = $favoriteForm->createView();
        }

        return $this->render(
            [
                'query' => $query->getData(),
                'forms' => $forms,
            ]
        );
    }
}