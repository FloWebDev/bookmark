const page = {
    init: () => {
        page.domChangeListener();
        page.getLists();
    },
    getLists: () => {
        page.displayLoader(true);
        const xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.href, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.querySelector('#listContainer').innerHTML = xhr.response.form;
                } else {
                    console.error('Erreur')
                }
                page.displayLoader(false);
            }
        };
        xhr.send();
    },
    displayLoader: (bool) => {
        if (bool) {
            document.querySelector('#loaderContainer').style.display = 'block';
            document.querySelector('#listContainer').style = 'none';
        } else {
            document.querySelector('#loaderContainer').style.display = 'none';
            document.querySelector('#listContainer').style = 'block';
        }
    },
    displayListForm: () => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/listing/new', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                page.displayLoader(false);
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.querySelector('#listingFormModalLabel').textContent = xhr.response.formTitle;
                    document.querySelector('#listingFormContent').innerHTML = xhr.response.form;
                } else {
                    console.error('Erreur displayListForm')
                }
            }
        };
        xhr.send();
    },
    handleNewListForm: e => {
        e.preventDefault();
        const xhr = new XMLHttpRequest();
        const data = new FormData(e.target);
        xhr.open('POST', e.target.getAttribute('action'), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (xhr.response.success) {
                        page.closeModal();
                        page.getLists();
                    } else if (!xhr.response.success && xhr.response.form) {
                        document.querySelector('#listingFormModalLabel').textContent = xhr.response.formTitle;
                        document.querySelector('#listingFormContent').innerHTML = xhr.response.form;
                    }
                } else {
                    console.error('Erreur handleNewListForm');
                }
            }
        };
        xhr.send(data);
    },
    displayDeleteListForm: e => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/listing/' + e.currentTarget.getAttribute('data-list-id') + '/delete', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.querySelector('#deleteListingModalLabel').textContent = xhr.response.formTitle;
                    document.querySelector('#deleteListingModal div.modal-footer').innerHTML = xhr.response.form;
                } else {
                    console.error('Erreur displayDeleteListForm')
                }
            }
        };
        xhr.send();
    },
    handleDeleteListForm: e => {
        e.preventDefault();
        const xhr = new XMLHttpRequest();
        const data = new FormData(e.target);
        xhr.open('POST', e.target.getAttribute('action'), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    page.closeModal();
                    page.getLists();
                    if (!xhr.response.success) {
                        console.error('Erreur handleDeleteListForm');
                    }
                } else {
                    console.error('Erreur handleNewListForm');
                }
            }
        };
        xhr.send(data);
    },
    domChangeListener: () => {
        // Selectionne le noeud dont les mutations seront observées
        const targetNode = document.querySelector('section');

        // Options de l'observateur (quelles sont les mutations à observer)
        const config = {
            attributes: true,
            childList: true,
            subtree: true
        };

        // Créé une instance de l'observateur lié à la fonction de callback
        const observer = new MutationObserver(mutationsList => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    if (document.querySelector('[data-target="#listingModal"]')) {
                        document.querySelector('[data-target="#listingModal"]').addEventListener('click', page.displayListForm);
                    }
                    if (document.querySelector('#listingFormContent')) {
                        document.querySelector('#listingFormContent').addEventListener('submit', page.handleNewListForm)
                    }
                    if (document.querySelector('#listing_page')) {
                        document.querySelector('#listing_page').value = document.querySelector('section[data-page-id]').getAttribute('data-page-id');
                    }
                    if (document.querySelector('[data-target="#deleteListingModal"]')) {
                        document.querySelectorAll('[data-target="#deleteListingModal"]').forEach(elt => {
                            elt.addEventListener('click', page.displayDeleteListForm);
                        });
                    }
                    if (document.querySelector('#deleteListingFormBtn')) {
                        document.querySelector('#deleteListingFormBtn').addEventListener('submit', page.handleDeleteListForm);
                    }
                }
            }
        });

        // Commence à observer le noeud cible pour les mutations précédemment configurées
        observer.observe(targetNode, config);
    },
    closeModal: () => {
        document.querySelectorAll('button[data-dismiss="modal"').forEach(elt => {
            elt.click();
        });
    }
};
document.addEventListener('DOMContentLoaded', page.init);