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

function addToFavorite(e)
{
    let button = e.currentTarget;
    let svgButton = button.querySelector('svg');

    if(!svgButton)
    {
        return;
    }

    let invariable = svgButton.dataset.invariable;

    fetch('/favorite/new/' + invariable, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(button.closest('form'))
    })
        .then(response => response.json())
        .then(() =>
        {
            toggleFavorite(svgButton);

            // Если это кнопка удаления — удаляем карточку
            if(button.classList.contains('favorite-delete'))
            {
                let card = button.closest('form')?.closest('.card')?.closest('.col');
                if(card)
                {
                    card.classList.add('fade-delete'); // Добавляем анимацию
                    setTimeout(() => card.remove(), 500);
                }
            }
        })
        .catch(error => console.error('Ошибка запроса:', error));
}

function toggleFavorite(svgButton)
{
    if(!svgButton)
    {
        return;
    }
    svgButton.classList.toggle('text-secondary');
    svgButton.classList.toggle('text-primary');
}

allFavorites = document.querySelectorAll('.favorite, .favorite-delete');

allFavorites.forEach(favorite =>
{

    favorite.addEventListener('click', function(e)
    {

        e.preventDefault();
        addToFavorite(e);
    }, true);
});