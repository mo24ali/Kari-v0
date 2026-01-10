<?php $title = "Détails du Logement - KARI"; ?>
<?php ob_start(); ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Logement Header & Images -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="relative h-96">
            <?php
            $primaryImage = $logement->getPrimaryImage() ?? 'https://via.placeholder.com/800x600';
            // Adjust path if relative
            if ($primaryImage && !str_starts_with($primaryImage, 'http')) {
                $primaryImage = '/assets/uploads/' . basename($primaryImage);
            }
            ?>
            <img src="<?= htmlspecialchars($primaryImage) ?>" alt="Logement" class="w-full h-full object-cover">
            <div class="absolute top-4 right-4 bg-white px-4 py-2 rounded-full font-bold text-blue-600 shadow">
                <?= htmlspecialchars($logement->getPrice()) ?> € / nuit
            </div>
        </div>

        <!-- Gallery (Thumbnail) -->
        <?php if (!empty($logement->getImages())): ?>
            <div class="flex gap-2 p-4 overflow-x-auto">
                <?php foreach ($logement->getImages() as $img): ?>
                    <?php
                    $imgPath = $img['image_path'];
                    if (!str_starts_with($imgPath, 'http'))
                        $imgPath = '/assets/uploads/' . basename($imgPath);
                    ?>
                    <img src="<?= htmlspecialchars($imgPath) ?>"
                        class="w-24 h-24 object-cover rounded cursor-pointer hover:opacity-75 transition">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($logement->getAddress()) ?></h1>
            <div class="flex items-center mb-6">
                <!-- Average Rating -->
                <div class="flex items-center text-yellow-500 mr-4">
                    <span class="text-xl font-bold mr-1"><?= number_format($averageRating, 1) ?></span>
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                        <path
                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                    </svg>
                </div>
                <!-- Owner Info -->
                <?php $owner = $logement->getOwner(); ?>
                <div class="text-gray-600">
                    Hôte : <span
                        class="font-semibold"><?= htmlspecialchars($owner ? $owner['firstname'] . ' ' . $owner['lastname'] : 'Inconnu') ?></span>
                </div>
            </div>

            <!-- Reservation Form Block -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $logement->getIdOwner()): ?>
                <div class="mt-8 bg-blue-50 p-6 rounded-lg border border-blue-100">
                    <h3 class="text-xl font-semibold mb-4 text-blue-800">Réserver ce logement</h3>
                    <form action="/reservation/create" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="hidden" name="id_log" value="<?= $logement->getId() ?>">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Arrivée</label>
                            <input type="date" name="start_date" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Départ</label>
                            <input type="date" name="end_date" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition">
                                Vérifier & Réserver
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold mb-6">Avis (<?= count($reviews) ?>)</h2>

        <?php if (empty($reviews)): ?>
            <p class="text-gray-500 italic">Aucun avis pour le moment.</p>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($reviews as $avis): ?>
                    <div class="border-b pb-6 last:border-0">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span
                                    class="font-bold text-gray-800"><?= htmlspecialchars($avis->getAuthorName() ?? 'Utilisateur') ?></span>
                                <span class="text-xs text-gray-500 ml-2"><?= $avis->getCreatedAt() ?></span>
                            </div>
                            <div class="flex text-yellow-500">
                                <?php for ($i = 0; $i < $avis->getRating(); $i++)
                                    echo '★'; ?>
                            </div>
                        </div>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($avis->getComment())) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require_once __DIR__ . '/layout.php'; ?>