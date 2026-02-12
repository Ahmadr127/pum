const Toast = {
    show(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-5 right-5 z-50 px-6 py-4 rounded shadow-lg transform transition-all duration-300 translate-y-full opacity-0 flex items-center gap-3`;

        // Colors based on type
        if (type === 'success') {
            toast.className += ' bg-green-500 text-white';
            toast.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
        } else if (type === 'error') {
            toast.className += ' bg-red-500 text-white';
            toast.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${message}</span>`;
        } else {
            toast.className += ' bg-gray-800 text-white';
            toast.innerHTML = `<i class="fas fa-info-circle"></i><span>${message}</span>`;
        }

        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
        });

        // Remove after duration
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, duration);
    },

    success(message, options = {}) {
        this.show(message, 'success', options.duration);
    },

    error(message, options = {}) {
        this.show(message, 'error', options.duration);
    },

    info(message, options = {}) {
        this.show(message, 'info', options.duration);
    }
};

window.Toast = Toast;
