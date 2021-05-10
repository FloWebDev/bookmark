const app = {
    init: () => {
        if (document.querySelector('#flashMessages')) {
            setTimeout(() => {
                document.querySelector('#flashMessages').style.display = 'none';
            }, 5000);
        }
    }
}
document.addEventListener('DOMContentLoaded', app.init);