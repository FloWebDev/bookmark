const page = {
    currentListing: null,
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
    displayForm: e => {
        if (e.currentTarget.getAttribute('data-list-id')) {
            page.currentListing = e.currentTarget.getAttribute('data-list-id');
        }
        const xhr = new XMLHttpRequest();
        xhr.open('GET', e.currentTarget.getAttribute('data-action'), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                page.displayLoader(false);
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.querySelector('#formModalLabel').textContent = xhr.response.formTitle;
                    document.querySelector('#formContent').innerHTML = xhr.response.form;
                } else {
                    console.error('Erreur displayForm')
                }
            }
        };
        xhr.send();
    },
    handleSubmitForm: e => {
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
                        document.querySelector('#formModalLabel').textContent = xhr.response.formTitle;
                        document.querySelector('#formContent').innerHTML = xhr.response.form;
                    }
                } else {
                    console.error('Erreur handleSubmitForm');
                }
            }
        };
        xhr.send(data);
    },
    // Méthode générique à plusieurs entités d'affichage du formulaire de suppression dans la modale
    displayDeleteForm: e => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', e.currentTarget.getAttribute('data-action'), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.querySelector('#deleteModalLabel').textContent = xhr.response.title;
                    document.querySelector('#deleteModal div.modal-body').innerHTML = xhr.response.alert;
                    document.querySelector('#deleteFormContainer').innerHTML = xhr.response.form;
                } else {
                    console.error('Erreur displayDeleteForm')
                }
            }
        };
        xhr.send();
    },
    // Méthode générique à plusieurs entités du traitement de la soummission du formulaire de suppression présent dans la modale
    handleDeleteForm: e => {
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
                        console.error('Erreur handleDeleteForm');
                    }
                } else {
                    console.error('Erreur handleDeleteForm');
                }
            }
        };
        xhr.send(data);
    },
    // Méthode de suppression spécifique aux items
    handleItemDeleteClick: e => {
        e.preventDefault();
        const xhr = new XMLHttpRequest();
        const data = new FormData();
        data.set('_token', e.currentTarget.getAttribute('data-csrf'))
        xhr.open('POST', e.currentTarget.getAttribute('data-action'), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    page.closeModal();
                    page.getLists();
                    if (!xhr.response.success) {
                        console.error('Erreur handleItemDeleteClick');
                    }
                } else {
                    console.error('Erreur handleItemDeleteClick');
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
                    if (document.querySelector('[data-target="#modal"]')) {
                        document.querySelectorAll('[data-target="#modal"]').forEach(elt => {
                            elt.addEventListener('click', page.displayForm);
                        });

                    }
                    if (document.querySelector('#formContent')) {
                        document.querySelector('#formContent').addEventListener('submit', page.handleSubmitForm)
                    }
                    if (document.querySelector('#listing_page')) {
                        document.querySelector('#listing_page').value = document.querySelector('section[data-page-id]').getAttribute('data-page-id');
                    }
                    if (document.querySelector('#item_listing')) {
                        document.querySelector('#item_listing').value = page.currentListing;
                    }
                    if (document.querySelector('[data-target="#deleteModal"]')) {
                        document.querySelectorAll('[data-target="#deleteModal"]').forEach(elt => {
                            elt.addEventListener('click', page.displayDeleteForm);
                        });
                    }
                    if (document.querySelector('#deleteModal form')) {
                        document.querySelector('#deleteModal form').addEventListener('submit', page.handleDeleteForm);
                    }
                    if (document.querySelector('.itemDeleteBtn')) {
                        document.querySelectorAll('.itemDeleteBtn').forEach(elt => {
                            elt.addEventListener('click', page.handleItemDeleteClick);
                        });
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