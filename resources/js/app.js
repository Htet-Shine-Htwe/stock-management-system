import "../css/app.scss"

require('bootstrap')


function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.classList.add('notification', type);
    notification.textContent = message;

    document.body.appendChild(notification);

    // Auto-remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}  


window.showNotification = showNotification;