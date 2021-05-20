const page = {
    currentListing: null,
    // Méthode d'initialisation
    init: () => {
        page.domChangeListener();
        page.getLists();
    },
    // Méthode de récupération de toutes les listes + items
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
    // Méthode permettant d'activer/désactiver le spinner/loader
    displayLoader: (bool) => {
        if (bool) {
            document.querySelector('#loaderContainer').style.display = 'block';
            document.querySelector('#listContainer').style = 'none';
        } else {
            document.querySelector('#loaderContainer').style.display = 'none';
            document.querySelector('#listContainer').style = 'block';
        }
    },
    // Méthode générique permettant d'afficher un formulaire
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
    // Méthode générique permettant de traiter la soumission d'un formulaire
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
    // Méthode générique permettant l'affichage d'un formulaire de suppression dans la modale
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
    // Méthode générique permettant de traiter la soumission d'un formulaire de suppresion présent dans une modale
    handleDeleteForm: e => {
        e.preventDefault();
        const xhr = new XMLHttpRequest();
        const data = new FormData(e.target);
        xhr.open('POST', e.currentTarget.getAttribute('action'), true);
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
    // Méthode de suppression spécifique aux items (ne dépendant pas d'un formulaire)
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
    // Méthode permettant de gérer les changements dans l'ordre
    handleUpAndDown: e => {
        e.preventDefault();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', e.currentTarget.getAttribute('data-action'), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    page.getLists();
                    if (!xhr.response.success) {
                        console.error('Erreur handleUpAndDown');
                    }
                } else {
                    console.error('Erreur handleUpAndDown');
                }
            }
        };
        xhr.send();
    },
    // Méthode permettant de gérer les écouteurs sur des éléments créés APRES le chargement du DOM initial (utilise MutationObserver)
    domChangeListener: () => {
        // Sélectionne le noeud dont les mutations seront observées
        const targetNode = document.querySelector('section');

        // Options de l'observateur (quelles sont les mutations à observer)
        const config = {
            attributes: true,
            childList: true,
            subtree: true
        };

        // Crée une instance de l'observateur lié à la fonction de callback
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
                    if (document.querySelector('.change-order')) {
                        document.querySelectorAll('.change-order').forEach(elt => {
                            elt.addEventListener('click', page.handleUpAndDown);
                        });
                    }
                    if (document.querySelector('#item_url')) {
                        document.querySelector('#item_url').addEventListener('change', page.getTitlePageFromExternalUrl);
                        // document.querySelector('#item_title').addEventListener('change', page.getTitlePageFromExternalUrl);
                    }
                }
            }
        });

        // Commence à observer le noeud cible pour les mutations précédemment configurées
        observer.observe(targetNode, config);
    },
    // Méthode permettant de fermer l'ensemble des modales
    closeModal: () => {
        document.querySelectorAll('button[data-dismiss="modal"').forEach(elt => {
            elt.click();
        });
    },
    // Méthode permettant d'obtenir le titre d'une page à partir d'un lien externe et d'effectuer l'autocomplétion dans
    // le champ titre du formulaire de l'item
    getTitlePageFromExternalUrl: e => {
        const url = e.target.value;
        if (url.length > 10 && url.startsWith('http://') || url.startsWith('https://')) {
            const xhr = new XMLHttpRequest();
            data = new FormData();
            data.set('url', url);
            xhr.open('POST', titlePageServicePath, true); // titlePageService définie dans page/show.html.twig
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.responseType = 'json';
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (xhr.response) {
                            document.querySelector('#item_title').value = xhr.response;
                        }
                    } else {
                        console.error('Erreur getTitlePageFromExternalUrl');
                    }
                }
            };
            xhr.send(data);
        }
    }
};
document.addEventListener('DOMContentLoaded', page.init);