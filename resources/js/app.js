import './bootstrap';

window.Echo.channel('notification')
    .listen('ChangesNotification', (e) => {
        if (e.changes === 'Table data changed') {
            reload('/track');
        }
    });