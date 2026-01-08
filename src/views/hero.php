<?php


require_once 'partials/head.php';
require_once 'partials/nav.php';
?>

<div class="bg-white dark:bg-gray-900">

    <div class="relative isolate px-6 pt-14 lg:px-8">
        
        <div class="mx-auto max-w-2xl sm:py-48 lg:py-56">

            <div class="text-center">   
                <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl dark:text-white">Welcome to Kari here to find your next residence</h1>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="/login" class="rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                        Se connecter
                    </a>
                    <a href="/signup" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once 'partials/script.php';
require_once 'partials/footer.php';
?>