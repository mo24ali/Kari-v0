<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('i');

    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    }

    themeToggle.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');

        if (document.documentElement.classList.contains('dark')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        }
    });

    const userMenuBtn = document.getElementById('user-menu-btn');
    const userMenu = document.getElementById('user-menu');

    if (userMenuBtn && userMenu) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }

    const searchButton = document.querySelector('button:has(.fa-search)');
    if (searchButton) {
        searchButton.addEventListener('click', (e) => {
            // Let the form submit normally (GET request)
        });
    }

    document.querySelectorAll('.w-8.h-8.border').forEach(button => {
        button.addEventListener('click', function () {
            document.querySelectorAll('.w-8.h-8.bg-primary').forEach(selected => {
                selected.classList.remove('bg-primary', 'text-white');
                selected.classList.add('border', 'border-light');
            });

            if (!this.querySelector('i')) {
                this.classList.remove('border', 'border-light');
                this.classList.add('bg-primary', 'text-white');
            }
        });
    });

    // Notifications Toggle
    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
            // Close user menu if open
            if (userMenu) userMenu.classList.add('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }

    async function markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-as-read', {
                method: 'POST'
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }
</script>