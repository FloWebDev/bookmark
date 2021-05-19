const dashboard = {
    init: () => {
        document.querySelectorAll('.picture').forEach(elt => {
            elt.addEventListener('click', dashboard.handleClickPicture)
        });
    },
    // Méthode permettant de gérer le changement de wallpaper
    handleClickPicture: e => {
        const xhr = new XMLHttpRequest();
        data = new FormData();
        data.set('wallpaper', e.currentTarget.getAttribute('data-wallpaper'));
        xhr.open('POST', changeWallpaperPath, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (xhr.response && xhr.response.success) {
                        document.location.reload();
                    } else {
                        console.error('Erreur handleClickPicture');
                    }
                } else {
                    console.error('Erreur handleClickPicture');
                }
            }
        };
        xhr.send(data);
    }
};
document.addEventListener('DOMContentLoaded', dashboard.init);