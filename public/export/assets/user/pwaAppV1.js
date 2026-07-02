let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Custom popup বানানো
    const popup = document.createElement('div');
    popup.innerHTML = `
        <div id="pwa-popup" style="
            position: fixed;
            bottom: 70px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg,#0F0C29,#302B63,#24243e);
            color: #fff;
            padding: 12px 15px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
            z-index: 2000;
            width: 95%;
            max-width: 420px;
            font-family: 'Inter', sans-serif;
            animation: slideUp 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        ">
            <!-- Logo -->
            <img src="https://Codzshop.com/icon.png" alt="logo" style="
                width: 48px;
                height: 48px;
                border-radius: 12px;
                object-fit: cover;
                flex-shrink: 0;
            ">

            <!-- Title + Description -->
            <div style="flex: 1; text-align: left;">
                <h3 style="margin:0; font-size: 1rem; font-weight: 700;">Install Codzshop</h3>
                <p style="margin:4px 0 0; font-size: 0.85rem; opacity: 0.9;">আমাদের App ডাউনলোড করুন</p>
            </div>

            <!-- Actions -->
            <div style="display: flex; flex-direction: column; gap: 4px; text-align: right;">
                <span id="installBtn" style="
                    cursor: pointer;
                    font-size: 0.9rem;
                    font-weight: 600;
                    color: #00d4ff;
                ">Install</span>
            </div>
        </div>

        <style>
        @keyframes slideUp {
            from { transform: translate(-50%, 100%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
        </style>
    `;
    document.body.appendChild(popup);

    // Install button click
    document.getElementById('installBtn').addEventListener('click', async () => {
    popup.remove();
    deferredPrompt.prompt();
    const choice = await deferredPrompt.userChoice;
    console.log('User choice:', choice.outcome);
    deferredPrompt = null;
});

    // Close button click
    document.getElementById('closeBtn').addEventListener('click', () => {
    popup.remove();
});
});

