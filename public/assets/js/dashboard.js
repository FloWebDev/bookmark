const dashboard = {
    init: () => {
        document.querySelectorAll('.picture').forEach(elt => {
            elt.addEventListener('click', dashboard.handleClickPicture)
        });
    },
    // Méthode permettant de gérer le changement de wallpaper
    handleClickPicture: e => {
        const wallpaper = e.currentTarget.getAttribute('data-wallpaper');
        const xhr = new XMLHttpRequest();
        data = new FormData();
        data.set('wallpaper', wallpaper);
        xhr.open('POST', changeWallpaperPath, true); // // changeWallpaperPath définie dans dashboard/index.html.twig
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'json';
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (xhr.response && xhr.response.success) {
                        document.querySelector('body').style.backgroundImage = "url('assets/wallpapers/" + wallpaper + "')";
                        console.log(document.querySelectorAll('#galery .picture'))
                        document.querySelectorAll('#gallery div.picture').forEach(picture => {
                            if (picture.getAttribute('data-wallpaper') === wallpaper) {
                                picture.classList.remove('active'); // évite d'avoir plusieurs fois la même classe
                                picture.classList.add('active');
                            } else {
                                picture.classList.remove('active');
                            }
                        });
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