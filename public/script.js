if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js')
        .then(function () {
            console.log('Service Worker OK')
        })
        //.catch(function (e) { console.error(e + '...') });
}