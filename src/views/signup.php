<?php
require_once 'partials/head.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-home text-white text-lg"></i>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Créer un compte KARI
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Ou
                <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                    connectez-vous à votre compte
                </a>
            </p>
        </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="/signup" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="firstname" class="sr-only">Prénom</label>
                        <input id="firstname" name="firstname" type="text" required
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Prénom"
                            value="<?php echo htmlspecialchars($_SESSION['old']['firstname'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="lastname" class="sr-only">Nom</label>
                        <input id="lastname" name="lastname" type="text" required
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Nom"
                            value="<?php echo htmlspecialchars($_SESSION['old']['lastname'] ?? ''); ?>">
                    </div>
                </div>

                         <div>
                    <label for="email" class="sr-only">Adresse email</label>
                    <input id="email" name="email" type="email" required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Adresse email"
                        value="<?php echo htmlspecialchars($_SESSION['old']['email'] ?? ''); ?>"
                        autocomplete="email">
                </div>

                             <div>
                    <label for="phone" class="sr-only">Téléphone (optionnel)</label>
                    <input id="phone" name="phone" type="tel"
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Téléphone (optionnel)"
                        value="<?php echo htmlspecialchars($_SESSION['old']['phone'] ?? ''); ?>"
                        autocomplete="tel">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Type de compte
                    </label>
                    <select id="role" name="role"
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="traveller" <?php echo (($_SESSION['old']['role'] ?? 'traveller') === 'traveller') ? 'selected' : ''; ?>>Voyageur</option>
                        <option value="host" <?php echo (($_SESSION['old']['role'] ?? 'traveller') === 'host') ? 'selected' : ''; ?>>Hôte</option>

                    </select>
                </div>

                                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Mot de passe (min. 8 caractères)"
                        autocomplete="new-password">
                </div>

                <div>
                    <label for="confirm_password" class="sr-only">Confirmer le mot de passe</label>
                    <input id="confirm_password" name="confirm_password" type="password" required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Confirmer le mot de passe"
                        autocomplete="new-password">
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                    J'accepte les
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                        conditions d'utilisation
                    </a>
                </label>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-indigo-500 group-hover:text-indigo-400"></i>
                    </span>
                    Créer mon compte
                </button>
            </div>
        </form>
    </div>
</div>

<?php
unset($_SESSION['old']);
require_once 'partials/footer.php';
?>