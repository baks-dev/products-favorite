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
        $user = $this->getUsr();
        $session = $request->getSession();
        $query = $allProductsFavorite;
        if($user === null)
        {
            $query = $query->session($session)->findPublicPaginator();
        } else {
            $query = $query->user($user)->findUserPaginator();
        }

        $forms = [];
        foreach($query->getData() as $product)
        {
            $ProductsFavoriteDTO = new AnonymousProductsFavoriteDTO();
            $ProductsFavoriteDTO->setInvariable($product['product_invariable_id']);

            $favoriteForm = $formFactory->createNamed(
                $product['product_invariable_id'],
                PublicProductsFavoriteForm::class, $ProductsFavoriteDTO,
                ['action' => $this->generateUrl('products-favorite:newedit.new', ['invariable' => $product['product_invariable_id']])]
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