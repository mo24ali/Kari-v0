<?php $title = "Détails du Logement - KARI"; ?>
<?php ob_start(); ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in duration-700">
    <!-- Logement Header & Images -->
    <div
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden mb-8 border border-gray-100 dark:border-gray-700 transition-colors">
        <div class="relative h-[50vh] min-h-[400px]">
            <?php
            $primaryImage = $logement->getPrimaryImage() ?? 'https://via.placeholder.com/800x600';
            ?>
            <img src="<?= htmlspecialchars($primaryImage) ?>" alt="Logement" class="w-full h-full object-cover">
            <div
                class="absolute top-6 right-6 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md px-6 py-3 rounded-2xl font-black text-primary shadow-2xl border border-white/20">
                <?= htmlspecialchars($logement->getPrice()) ?> € <span class="text-xs font-normal opacity-60">/
                    nuit</span>
            </div>
        </div>

        <!-- Gallery -->
        <?php if (!empty($logement->getImages())): ?>
            <div
                class="flex gap-4 p-6 overflow-x-auto bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                <?php foreach ($logement->getImages() as $img): ?>
                    <img src="<?= htmlspecialchars($img['image_path']) ?>"
                        class="w-28 h-28 object-cover rounded-2xl cursor-pointer hover:scale-105 active:scale-95 transition-all shadow-sm border-2 border-transparent hover:border-primary">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="p-8 lg:p-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2 leading-tight">
                        <?= htmlspecialchars($logement->getAddress()) ?>
                    </h1>
                    <div class="flex items-center gap-4">
                        <div
                            class="flex items-center text-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 px-3 py-1 rounded-full border border-yellow-100 dark:border-yellow-900/30">
                            <span class="text-sm font-bold mr-1"><?= number_format($averageRating, 1) ?></span>
                            <i class="fas fa-star text-xs"></i>
                        </div>
                        <?php $owner = $logement->getOwner(); ?>
                        <div class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <i class="fas fa-user-tie text-sm"></i>
                            </div>
                            <span class="text-sm">Hôte : <span
                                    class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($owner ? $owner['firstname'] . ' ' . $owner['lastname'] : 'Inconnu') ?></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed mb-10">
                <p>Découvrez ce magnifique logement situé à <?= htmlspecialchars($logement->getAddress()) ?>. Profitez
                    d'un séjour exceptionnel avec tout le confort nécessaire.</p>
            </div>

            <!-- Reservation Form Block -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $logement->getIdOwner()): ?>
                <div
                    class="bg-primary/5 dark:bg-primary/10 p-8 rounded-3xl border border-primary/10 dark:border-primary/20 shadow-inner">
                    <h3 class="text-2xl font-black mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-calendar-check text-primary"></i> Réserver votre séjour
                    </h3>
                    <form action="/reservation/create" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <input type="hidden" name="id_log" value="<?= $logement->getId() ?>">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Date d'arrivée</label>
                            <input type="date" name="start_date" required
                                class="w-full bg-white dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all dark:text-white">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Date de départ</label>
                            <input type="date" name="end_date" required
                                class="w-full bg-white dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all dark:text-white">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-primary text-white font-black py-4 px-6 rounded-2xl hover:bg-primary-dark transition-all transform active:scale-95 shadow-xl shadow-primary/20 flex items-center justify-center gap-2">
                                <span>Confirmer la réservation</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews Section -->
    <div
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-8 lg:p-12 border border-gray-100 dark:border-gray-700 transition-colors">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white">
                Expériences voyageurs <span class="text-primary opacity-40"><?= count($reviews) ?></span>
            </h2>
        </div>

        <?php if (empty($reviews)): ?>
            <div
                class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <i class="far fa-comment-dots text-4xl text-gray-300 dark:text-gray-600 mb-4 block"></i>
                <p class="text-gray-500 dark:text-gray-400 italic font-medium">Aucun avis encore. Soyez le premier à
                    partager votre expérience !</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <?php foreach ($reviews as $avis): ?>
                    <div
                        class="bg-gray-50 dark:bg-gray-900/30 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 hover:border-primary/30 transition-all group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white font-black text-lg shadow-lg">
                                    <?= strtoupper(substr($avis->getAuthorName() ?? 'U', 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-black text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($avis->getAuthorName() ?? 'Utilisateur') ?>
                                    </div>
                                    <div class="text-xs text-secondary font-medium uppercase tracking-wider">
                                        <?= date('d M Y', strtotime($avis->getCreatedAt())) ?>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex text-yellow-500 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-full shadow-sm border border-gray-100 dark:border-gray-700">
                                <?php for ($i = 0; $i < $avis->getRating(); $i++): ?>
                                    <i class="fas fa-star text-[10px]"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 italic leading-relaxed font-medium">
                            "<?= nl2br(htmlspecialchars($avis->getComment())) ?>"
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// $content = ob_get_clean();
?>

<?php require_once __DIR__ . '/layout.php'; ?>