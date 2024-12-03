<div id="loading-spinner" class="absolute inset-0 flex items-center justify-center bg-gray-100 z-50">
    <div class="dots-loading">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>

<style>
    .dots-loading {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dots-loading .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #3b82f6;
        /* blue-500 */
        animation: dot-pulse 1.5s infinite;
    }

    .dots-loading .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dots-loading .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dot-pulse {

        0%,
        100% {
            opacity: 0.2;
            transform: scale(0.8);
        }

        50% {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
