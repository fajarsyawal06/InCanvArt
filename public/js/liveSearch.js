document.addEventListener('DOMContentLoaded', function () {
    const input   = document.getElementById('navbar-search');
    if (!input) return;

    const modal   = document.getElementById('navbar-search-modal');
    const results = document.getElementById('navbar-search-results');
    const endpoint = input.dataset.userSearchUrl;

    let typingTimer = null;

    function hideModal() {
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function showModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
    }

    function renderResults(users) {
        if (!results) return;

        if (!users.length) {
            results.innerHTML = `
                <p class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                    Tidak ada akun ditemukan.
                </p>
            `;
            return;
        }

        results.innerHTML = users.map(user => {
            const safeBio = user.bio ? user.bio.substring(0, 80) : '';
            return `
                <button
                    type="button"
                    class="w-full text-left px-4 py-2 flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-[#443C68]"
                    onclick="window.location.href='${user.profile_url}'"
                >
                    <img
                        src="${user.avatar}"
                        alt="Avatar ${user.username}"
                        class="w-9 h-9 rounded-full object-cover"
                        loading="lazy"
                    >
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            ${user.username}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            ${user.email ?? ''}
                        </p>
                        ${safeBio
                            ? `<p class="mt-0.5 text-xs text-gray-400 dark:text-gray-400 truncate">${safeBio}</p>`
                            : ''
                        }
                    </div>
                </button>
            `;
        }).join('');
    }

    async function performSearch(query) {
        const q = query.trim();
        if (q.length < 2) {
            hideModal();
            return;
        }

        try {
            const url = `${endpoint}?q=${encodeURIComponent(q)}`;
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                hideModal();
                return;
            }

            const data = await response.json();
            renderResults(data.users || []);
            showModal();
        } catch (error) {
            console.error('Live search error:', error);
            hideModal();
        }
    }

    // Debounce input
    input.addEventListener('input', function (e) {
        const value = e.target.value;

        if (typingTimer) {
            clearTimeout(typingTimer);
        }

        if (!value.trim()) {
            hideModal();
            return;
        }

        typingTimer = setTimeout(() => {
            performSearch(value);
        }, 250); // 250ms delay
    });

    // Fokus: kalau ada teks dan sudah pernah search, buka lagi modal
    input.addEventListener('focus', function () {
        if (results && results.innerHTML.trim() !== '') {
            showModal();
        }
    });

    // Klik di luar modal → sembunyikan
    document.addEventListener('click', function (e) {
        if (!modal) return;
        if (e.target === input || input.contains(e.target)) return;
        if (modal.contains(e.target)) return;
        hideModal();
    });

    // ESC untuk tutup
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            hideModal();
            input.blur();
        }
    });
});

