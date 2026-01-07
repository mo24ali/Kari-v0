<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

$user = $_SESSION['user'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userEmail = $_SESSION['user_email'] ?? '';
$userRole = $_SESSION['user_role'] ?? 'traveller';
$userFirstname = $_SESSION['user_firstname'] ?? '';
$userLastname = $_SESSION['user_lastname'] ?? '';
$userPhone = $user['phone'] ?? $_SESSION['user_phone'] ?? '';

$initials = '';
if ($userFirstname && $userLastname) {
    $initials = strtoupper(substr($userFirstname, 0, 1) . substr($userLastname, 0, 1));
} elseif ($userName) {
    $parts = explode(' ', $userName);
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
} else {
    $initials = 'U';
}
?>

<div class="min-h-screen bg-surface py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">Mon Profil</h1>
            <p class="text-secondary">Gérez vos informations personnelles</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6 mb-6">
            <div class="flex items-center space-x-6 mb-6">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                    <?php echo htmlspecialchars($initials); ?>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($userName); ?></h2>
                    <p class="text-secondary mb-2"><?php echo htmlspecialchars($userEmail); ?></p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        <?php 
                        $roleLabels = [
                            'admin' => 'Administrateur',
                            'host' => 'Hôte',
                            'traveller' => 'Voyageur'
                        ];
                        echo htmlspecialchars($roleLabels[$userRole] ?? ucfirst($userRole)); 
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6">
            <h3 class="text-xl font-bold mb-6">Informations personnelles</h3>
            
            <form method="POST" action="/profile/update" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="firstname" class="block text-sm font-medium mb-2">Prénom</label>
                        <input type="text" 
                               id="firstname" 
                               name="firstname" 
                               value="<?php echo htmlspecialchars($_SESSION['old']['firstname'] ?? $userFirstname); ?>"
                               class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                               required>
                    </div>
                    
                    <div>
                        <label for="lastname" class="block text-sm font-medium mb-2">Nom</label>
                        <input type="text" 
                               id="lastname" 
                               name="lastname" 
                               value="<?php echo htmlspecialchars($_SESSION['old']['lastname'] ?? $userLastname); ?>"
                               class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                               required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($_SESSION['old']['email'] ?? $userEmail); ?>"
                               class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                               required>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-2">Téléphone</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?php echo htmlspecialchars($_SESSION['old']['phone'] ?? $userPhone); ?>"
                               class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="Ex: 0612345678">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 pt-4 border-t border-light">
                    <a href="/profile" 
                       class="px-6 py-2 border border-light rounded-lg hover:bg-surface transition-colors text-center">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
            <?php unset($_SESSION['old']); ?>
        </div>

        <div class="bg-surface rounded-lg shadow-custom-lg border border-light p-6 mt-6">
            <h3 class="text-xl font-bold mb-6">Changer le mot de passe</h3>
            
            <form method="POST" action="/profile/change-password" class="space-y-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium mb-2">Mot de passe actuel</label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                           required>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="new_password" class="block text-sm font-medium mb-2">Nouveau mot de passe</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                               required>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium mb-2">Confirmer le mot de passe</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="w-full px-4 py-2 border border-light rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                               required>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-light">
                    <button type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                        Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'partials/footer.php';
require_once 'partials/script.php';
?>
