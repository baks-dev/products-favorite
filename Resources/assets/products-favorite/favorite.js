function addToFavorite(e)
{
    let svgButton = e.currentTarget.querySelector('svg');
    let invariable = svgButton.attributes['data-invariable'].value;

    fetch('/favorite/new/' + invariable, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(e.target.closest('form'))
    })
        .then((json) => console.log(json))
        .then(toggleFavorite(svgButton));
}

function toggleFavorite(svgButton)
{
    //let svgButton = document.querySelector('.favorite svg');
    svgButton.classList.toggle('text-secondary');
    svgButton.classList.toggle('text-primary');
}

let allFavorites = document.querySelectorAll('.favorite');
allFavorites.forEach(favorite =>
    {
        favorite.addEventListener('click', function(e)
        {
            e.preventDefault();
            addToFavorite(e);
        }, true);
    }
)