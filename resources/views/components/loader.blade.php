
<div id="loader" class="loader-container">

    <i class="fa-solid fa-spinner loader-icon"></i>

</div>

<style>

    /* Loader wrapper */
    .loader-container {
        display: none;
        justify-content: center;
        align-items: center;
        padding: 40px 0;
    }

    /* Rotating icon */
    .loader-icon {
        font-size: 40px;
        animation: spin 1s linear infinite;
    }

    /* Rotation animation */
    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

</style>
