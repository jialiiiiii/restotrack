import './bootstrap';

window.Echo.channel('notification')
    .listen('ChangesNotification', (e) => {
        var page = window.location.pathname;

        if (e.changes === 'tables') { 
            if (page == '/track') {
                reload();
            }
        }
        else if (e.changes === 'orders') {
            if (page == '/orders/view') {
                reload();
            }
        }
        else if (e.changes === 'orders.manage') {
            if (page == '/orders') {
                reload();
            }
        }
        else if (e.changes === 'reservations.manage') {
            if (page == '/reservations') {
                reload();
            }
        }
    });