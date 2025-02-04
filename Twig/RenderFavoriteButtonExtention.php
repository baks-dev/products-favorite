<?php

namespace BaksDev\Products\Favorite\Twig;

use BaksDev\Auth\Email\Type\EmailStatus\EmailStatus;
use BaksDev\Products\Favorite\Entity\ProductsFavorite;
use BaksDev\Products\Favorite\Repository\DataUpdate\ProductsFavoriteDataUpdateInterface;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\Public\New\PublicProductsFavoriteForm;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileTokenStorage\UserProfileTokenStorage;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderFavoriteButtonExtention extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack,
        private UserProfileTokenStorage $userProfileTokenStorage,
        private FormFactoryInterface $formFactory,
        private ProductsFavoriteDataUpdateInterface $repository,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_favorite_button',
                [$this, 'renderButton'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function renderButton(Environment $twig, array $invariables): array
    {
        $isUsr = $this->userProfileTokenStorage->isUser();

        foreach($invariables as $invariable)
        {
            $ProductsFavoriteDTO =  new AnonymousProductsFavoriteDTO();
            $ProductsFavoriteDTO->setInvariable($invariable);

            $favoriteForm = $this->formFactory->createNamed(
                $invariable,
                PublicProductsFavoriteForm::class, $ProductsFavoriteDTO,
            );

            if (!$isUsr) {
                $session = $this->requestStack->getSession();
                $isFavorite = $session->get('favorite') === null ? false : isset($session->get('favorite')[$invariable]);
            } else {
                $usr = $this->userProfileTokenStorage->getUser();
                $isFavorite = $this->repository->user($usr)->invariable($invariable)->exists();
            }

            $forms[$invariable] = $twig->render('@products-favorite/public/index/pc/form/heart_button.html.twig', [
                    'form' => $favoriteForm->createView(),
                    'color_tag' => $isFavorite ? 'text-primary' : 'text-secondary',
            ]);
        }

        return $forms;
    }
}