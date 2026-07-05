function acceptCookies() {
    localStorage.setItem('cookiesAccepted', 'true');
    hidePopup();
}

function hidePopup() {
    document.getElementById('popup-container').style.display = 'none';
}

window.onload = function() {
    var cookiesAccepted = localStorage.getItem('cookiesAccepted');
    if (!cookiesAccepted) {
        document.getElementById('popup-container').style.display = 'block';
    }
};