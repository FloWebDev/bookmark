const page = {
    init: () => {
        page.getLists();
    },
    getLists: () => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.href, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'html'; // to avoid JSON.parse(xhr.response)
        xhr.onreadystatechange = () => {
            console.log(xhr.response)
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const res = xhr.response;
                    console.log('RES', xhr);
                    document.querySelector('#listContainer').innerHTML = res;
                } else {
                    console.error('Erreur')
                }
            }
        };
        setTimeout(() => {
        xhr.send(); 
        }, 5000);
    }
};
document.addEventListener('DOMContentLoaded', page.init);