const userDelete = {
    init: () => {
        document.querySelectorAll('.userDeleteBtn').forEach(elt => {
            elt.addEventListener('click', userDelete.handleClick);
        });
    },
    handleClick: e => {
        if (!window.confirm('Confirmez la suppression de l\'utilisateur.')) {
            e.preventDefault();
        }
    }
};
document.addEventListener('DOMContentLoaded', userDelete.init);