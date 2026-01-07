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
        
        document.querySelectorAll('.fa-heart').forEach(heart => {
            heart.addEventListener('click', function(e) {
                e.stopPropagation();
                if (this.classList.contains('text-secondary')) {
                    this.classList.remove('text-secondary');
                    this.classList.add('text-red-500');
                } else {
                    this.classList.remove('text-red-500');
                    this.classList.add('text-secondary');
                }
            });
        });
        
        const searchButton = document.querySelector('button:has(.fa-search)');
        if (searchButton) {
            searchButton.addEventListener('click', () => {
                const originalText = searchButton.innerHTML;
                searchButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Recherche...';
                searchButton.disabled = true;
                
                setTimeout(() => {
                    searchButton.innerHTML = originalText;
                    searchButton.disabled = false;
                    
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-custom-lg z-50';
                    notification.innerHTML = 'Recherche effectuée - 24 résultats trouvés';
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }, 1500);
            });
        }
        
        document.querySelectorAll('.w-8.h-8.border').forEach(button => {
            button.addEventListener('click', function() {
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
    </script>