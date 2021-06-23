/**
* JS file to deal with collection forms in the trick_create view
*/

/*To display new form_row on click in order to add/remove pictures or videos */

const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement('div');
    // item.classList.add('container');

    item.innerHTML += collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    item.querySelector('.btn-remove').addEventListener('click', () => item.remove());

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};

document
    .querySelectorAll('.btn-remove')
    .forEach(btn => btn.addEventListener('click', (e) => e.currentTarget.closest('.row').remove()));

document
    .querySelectorAll('.btn-new')
    .forEach(btn => btn.addEventListener('click', newItem));