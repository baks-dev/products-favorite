<?php

namespace BaksDev\Products\Favorite\Twig;

use BaksDev\Core\Twig\TemplateExtension;
use BaksDev\Products\Favorite\Repository\ExistProductsFavorite\ExistProductsFavoriteInterface;
use BaksDev\Products\Favorite\UseCase\Public\New\AnonymousProductsFavoriteDTO;
use BaksDev\Products\Favorite\UseCase\Public\New\PublicProductsFavoriteForm;
use BaksDev\Users\User\Repository\UserTokenStorage\UserTokenStorageInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderFavoriteButtonExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UserTokenStorageInterface $userTokenStorage,
        private readonly FormFactoryInterface $formFactory,
        private readonly ExistProductsFavoriteInterface $ExistProductsFavorite,
        private readonly TemplateExtension $template,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_favorite_button',
                [$this, 'renderButton'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),

            new TwigFunction(
                'array_render_favorites_forms',
                [$this, 'renderForms'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @example :
     * set forms = render_favorite_button(['2a1ea75d-c43a-7eb6-8236-71d3e3804671', '01cc2987-b6d5-7025-b518-2d5b7b6b4a10'])
     * {{ forms['2a1ea75d-c43a-7eb6-8236-71d3e3804671']|row }}
     */
    public function renderForms(Environment $twig, array $invariables): array
    {
        $forms = null;

        foreach($invariables as $invariable)
        {
            $forms[$invariable] = $this->renderButton($twig, $invariable);
        }

        return $forms;
    }

    /**
     * @example {{ render_favorite_button('2a1ea75d-c43a-7eb6-8236-71d3e3804671') }}
     */
    public function renderButton(Environment $twig, string $invariable): string
    {
        $isUsr = $this->userTokenStorage->isUser();

        $ProductsFavoriteDTO = new AnonymousProductsFavoriteDTO()
            ->setInvariable($invariable);

        $favoriteForm = $this->formFactory
            ->createNamed(
                name: $invariable,
                type: PublicProductsFavoriteForm::class,
                data: $ProductsFavoriteDTO,
            );

        if(!$isUsr)
        {
            $session = $this->requestStack->getSession();
            $isFavorite = $session->get('favorite') === null ? false : isset($session->get('favorite')[$invariable]);
        }
        else
        {
            $usr = $this->userTokenStorage->getUser();

            $isFavorite = $this->ExistProductsFavorite
                ->user($usr)
                ->invariable($invariable)
                ->exists();
        }

        $render = $this->template->extends('@products-favorite:render_favorite_button/content.html.twig');

        return $twig->render(
            $render,
            [
                'form' => $favoriteForm->createView(),
                'color_tag' => $isFavorite ? 'text-primary' : 'text-secondary',
            ]
        );

    }
}