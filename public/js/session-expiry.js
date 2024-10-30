document.addEventListener("DOMContentLoaded", function() {
    const sessionData = document.getElementById("session-data");
    const sessionLifetime = sessionData.dataset.sessionLifetime * 60 * 1000;
    const sessionExpiryTime = Date.now() + sessionLifetime;
    const redirectUrl = sessionData.dataset.redirectUrl;

    function checkSessionExpiry() {
        const currentTime = Date.now();

        if (currentTime >= sessionExpiryTime) {
            alert("Your session has expired due to inactivity.");
            window.location.href = redirectUrl;
        }
    }
    setTimeout(checkSessionExpiry, sessionLifetime);
});
