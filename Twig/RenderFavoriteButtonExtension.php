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
    public function renderButton(Environment $twig, string|null $invariable = null): string
    {
        if(is_null($invariable))
        {
            return '';
        }

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