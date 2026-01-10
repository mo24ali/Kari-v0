<?php
require_once 'partials/head.php';
require_once 'partials/nav.php';

use App\core\Database;
use App\Repositories\Impl\LogementRepository;
use App\Repositories\Impl\ImageRepository;
use App\Repositories\Impl\FavorisRepository;
use App\Services\LogementService;
use App\Services\FavorisService;

$logementRepository = new LogementRepository();
$imageRepository = new ImageRepository();
$logementService = new LogementService($logementRepository);

$logementService = new LogementService($logementRepository);

$filters = [
    'destination' => $_GET['destination'] ?? null,
    'check_in' => $_GET['check_in'] ?? null,
    'check_out' => $_GET['check_out'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'min_price' => $_GET['min_price'] ?? null
];

if (!empty($filters['destination']) || (!empty($filters['check_in']) && !empty($filters['check_out']))) {
    $logements = $logementService->searchLogements($filters);
} else if (!empty($filters['max_price']) && !empty($filters['min_price'])) {
    $logement = $logementService->searchLogementsByPrice($filters['max_price'], $filters['min_price']);
} else {
    $logements = $logementService->getAllLogements();
}


$userRole = $_SESSION['user_role'] ?? null;
$isHost = $userRole === 'host';
?>

<section class="relative  pt-12 pb-24 border-b border-gray-100 dark:border-gray-800 transition-colors duration-300">
    <div class="container mx-auto px-4 flex flex-col items-center">
        <div class="w-full max-w-5xl">
            <!-- Hero section -->
            <div class="text-center mb-12">
                <h1 class="text-5xl md:text-6xl font-bold tracking-tight  dark:text-white mb-6">
                    Trouvez votre séjour idéal sur <span class="text-primary">Kari</span>
                </h1>
                <p class="text-xl  dark:text-white font-medium max-w-2xl mx-auto">
                    Des logements uniques, pour chaque style de vie. Réservez facilement et en toute confiance.
                </p>
            </div>
            <!-- filter search -->
            <div class=" rounded-3xl p-8 shadow-2xl border border-gray-100 dark:border-gray-700 transition-all">
                <form method="post" action="/logement/filter">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- destination filter -->
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-primary"></i> Destination
                            </label>
                            <div class="relative">
                                <input type="text" name="destination"
                                    value="<?php echo isset($_GET['destination']) ? htmlspecialchars($_GET['destination']) : ''; ?>"
                                    class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none"
                                    placeholder="Où souhaitez-vous aller ?">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <!-- filter by date -->
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-primary"></i> Dates
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input type="date" name="check_in"
                                        value="<?php echo isset($_GET['check_in']) ? htmlspecialchars($_GET['check_in']) : ''; ?>"
                                        class="w-full pl-10 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none text-xs"
                                        placeholder="Arrivée" min="<?php echo date('Y-m-d'); ?>">
                                    <i
                                        class="fas fa-calendar-plus absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <div class="relative">
                                    <input type="date" name="check_out"
                                        value="<?php echo isset($_GET['check_out']) ? htmlspecialchars($_GET['check_out']) : ''; ?>"
                                        class="w-full pl-10 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none text-xs"
                                        placeholder="Départ" min="<?php echo date('Y-m-d'); ?>">
                                    <i
                                        class="fas fa-calendar-minus absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        <!-- filter by number of residents -->
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 flex items-center">
                                <i class="fas fa-user-friends mr-2 text-primary"></i> Voyageurs
                            </label>
                            <div class="relative">
                                <select
                                    class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white transition-all appearance-none outline-none">
                                    <option>1 voyageur</option>
                                    <option>2 voyageurs</option>
                                    <option>3 voyageurs</option>
                                    <option>4 voyageurs</option>
                                    <option>5+ voyageurs</option>
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <i
                                    class="fas fa-user-friends absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <!-- filter by price -->
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 flex items-center">
                                <i class="fas fa-money-alt mr-2 text-primary"></i> Price
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input type="" name="min_price"
                                        value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>"
                                        class="w-full pl-10 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none text-xs"
                                        placeholder="Max prix" min="<?php echo '0'; ?>">
                                    <i class="fa fa-money absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <div class="relative">
                                    <input type="" name="max_price"
                                        value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>"
                                        class="w-full pl-10 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none text-xs"
                                        placeholder="Min prix" min="<?php echo '0'; ?>">
                                    <i class="fa fa-money absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- filter by geographical preference -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="flex space-x-3 overflow-x-auto pb-2 w-full md:w-auto scrollbar-hide">
                            <button type="button"
                                class="category-tag active px-4 py-2 rounded-full border border-primary/20 bg-primary/5 text-primary text-sm font-bold whitespace-nowrap flex items-center">
                                <i class="fas fa-umbrella-beach mr-2"></i> Plage
                            </button>
                            <button type="button"
                                class="category-tag px-4 py-2 rounded-full border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-sm font-bold whitespace-nowrap flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-mountain mr-2"></i> Montagne
                            </button>
                            <button type="button"
                                class="category-tag px-4 py-2 rounded-full border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-sm font-bold whitespace-nowrap flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-city mr-2"></i> Ville
                            </button>
                            <button type="button"
                                class="category-tag px-4 py-2 rounded-full border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-sm font-bold whitespace-nowrap flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-swimming-pool mr-2"></i> Piscine
                            </button>
                        </div>

                        <button type="submit"
                            class="w-full md:w-auto bg-primary text-white font-black py-4 px-10 rounded-2xl hover:bg-primary-dark shadow-xl shadow-primary/30 transition-all transform hover:scale-105 flex items-center justify-center group uppercase tracking-widest text-sm">
                            <i class="fas fa-search mr-3"></i>
                            Rechercher
                            <i
                                class="fas fa-arrow-right ml-3 transform group-hover:translate-x-2 transition-transform"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap justify-center md:justify-start gap-8 mt-10">
                <div class="flex items-center">
                    <div
                        class="w-2.5 h-2.5 bg-green-500 rounded-full mr-3 animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.5)]">
                    </div>
                    <span class="text-gray-600 dark:text-gray-300 text-sm font-medium">
                        <span class="font-bold text-gray-900 dark:text-white"><?php echo count($logements); ?></span>
                        logements disponibles
                    </span>
                </div>
                <div class="flex items-center">
                    <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full mr-3 shadow-[0_0_10px_rgba(234,179,8,0.5)]">
                    </div>
                    <span class="text-gray-600 dark:text-gray-300 text-sm font-medium">
                        <span class="font-bold text-gray-900 dark:text-white">4.9</span> note moyenne
                    </span>
                </div>
                <div class="flex items-center">
                    <div class="w-2.5 h-2.5 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                    <span class="text-gray-600 dark:text-gray-300 text-sm font-medium">
                        <span class="font-bold text-gray-900 dark:text-white">24/7</span> support premium
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-12">

    <?php if (isset($_SESSION['success'])): ?>
        <div
            class="mb-8 flex items-center gap-3 rounded-2xl border border-green-200 dark:border-green-900/50 bg-green-50/50 dark:bg-green-900/20 px-6 py-4 font-medium text-green-800 dark:text-green-300 backdrop-blur-sm transition-all animate-fade-in">
            <i class="fas fa-check-circle text-xl"></i>
            <span><?php echo htmlspecialchars($_SESSION['success']); ?></span>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="mb-12 flex flex-col sm:flex-row items-end justify-between gap-6">
        <div class="max-w-xl">
            <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-2">
                Logements disponibles
            </h2>
            <div class="h-1.5 w-20 bg-primary rounded-full mb-4"></div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Parcourez nos meilleures offres sélectionnées pour
                vous.</p>
        </div>
        <?php if ($isHost): ?>
            <a href="/hote"
                class="group flex items-center px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-full shadow-lg shadow-primary/25 transition-all active:scale-95 font-bold gap-2">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                Ajouter un logement
            </a>
        <?php endif; ?>
    </div>

    <?php


    $favorisService = new FavorisService(new FavorisRepository());
    $userFavorites = [];
    if (isset($_SESSION['user_id'])) {
        $favs = $favorisService->getUserFavoris($_SESSION['user_id']);
        $userFavorites = array_column($favs, 'id');
    }
    ?>

    <?php if (empty($logements)): ?>
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <div class="bg-gray-100 dark:bg-gray-800/50 p-12 rounded-full mb-6 transition-colors duration-300">
                <i class="fas fa-home text-7xl text-gray-300 dark:text-gray-700"></i>
            </div>
            <p class="text-2xl font-bold text-gray-600 dark:text-gray-300">Aucun logement trouvé pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12">
            <?php foreach ($logements as $logement): ?>
                <div
                    class="group flex flex-col h-full bg-white dark:bg-gray-900 rounded-[2.5rem] overflow-hidden border border-transparent dark:border-gray-800 hover:shadow-2xl dark:hover:shadow-black/50 transition-all duration-500 hover:-translate-y-1">

                    <div class="relative aspect-[4/5] overflow-hidden rounded-[2rem] m-2.5">
                        <div class="airbnb-img-carousel h-full w-full relative">
                            <?php $carouselImages = $logement->getImages() ?? []; ?>
                            <?php if (!empty($carouselImages)): ?>
                                <?php foreach ($carouselImages as $idx => $img): ?>
                                    <img src="<?php echo htmlspecialchars($img['image_path']); ?>"
                                        class="h-full w-full object-cover transition-all duration-700 group-hover:scale-105 <?php echo $idx === 0 ? 'active' : 'hidden'; ?>"
                                        alt="Logement" data-index="<?php echo $idx; ?>">
                                <?php endforeach; ?>

                                <div
                                    class="absolute inset-0 flex items-center justify-between px-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                    <button
                                        class="airbnb-carousel-btn left pointer-events-auto w-8 h-8 rounded-full bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white flex items-center justify-center shadow-md hover:scale-110 transition-transform">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </button>
                                    <button
                                        class="airbnb-carousel-btn right pointer-events-auto w-8 h-8 rounded-full bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-white flex items-center justify-center shadow-md hover:scale-110 transition-transform">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-300 dark:text-gray-700"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php
                        $isFav = in_array($logement->getId(), $userFavorites);
                        // to toggle the favoris button
                        $action = $isFav ? '/favoris/remove' : '/favoris/add';
                        $iconClass = $isFav ? 'fas fa-heart text-red-500' : 'far fa-heart text-white';
                        ?>
                        <div class="absolute top-4 right-4 z-10">
                            <form method="POST" action="<?php echo $action; ?>">
                                <input type="hidden" name="logement_id"
                                    value="<?php echo htmlspecialchars($logement->getId()); ?>">
                                <button type="submit"
                                    class="p-2 rounded-full bg-black/20 hover:bg-white/20 backdrop-blur-sm transition-all hover:scale-110 group-list-btn">
                                    <i class="<?php echo $iconClass; ?> text-2xl drop-shadow-md"></i>
                                </button>
                            </form>
                        </div>
                        <?php if ($userRole === 'host' && $logement->getIdOwner() == ($_SESSION['user_id'] ?? null)): ?>
                            <span
                                class="absolute top-4 left-4 bg-primary text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg">
                                Votre annonce
                            </span>
                        <?php endif; ?>

                        <a href="/logement/details?id=<?= $logement->getId() ?>"
                            class="absolute top-4 left-4 bg-primary text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-primary-dark transition-colors <?php echo ($userRole === 'host' && $logement->getIdOwner() == ($_SESSION['user_id'] ?? null)) ? 'mt-10' : ''; ?>">
                            Voir en détails
                        </a>

                    </div>

                    <div class="px-5 pb-6 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate">
                                <?php echo htmlspecialchars($logement->getAddress() ?? "Magnifique Logement"); ?>
                            </h3>
                            <div class="flex items-center gap-1 shrink-0 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-lg">
                                <i class="fas fa-star text-[10px] text-yellow-500"></i>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">4.9</span>
                            </div>
                        </div>

                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 flex items-center gap-1">
                            <i class="fas fa-map-marker-alt text-[10px]"></i>
                            <?php echo htmlspecialchars($logement->getAddress() ?? 'France'); ?>
                        </p>

                        <div class="mt-auto space-y-4">
                            <div class="flex items-baseline gap-1 text-gray-900 dark:text-white">
                                <span
                                    class="text-2xl font-black"><?php echo number_format($logement->getPrice(), 0, ',', ' '); ?>
                                    €</span>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">/ nuit</span>
                            </div>

                            <?php if ($userRole && $userRole !== 'host' && $logement->getIdOwner() != ($_SESSION['user_id'] ?? 0)): ?>
                                <form method="POST" action="/reservation/create" class="space-y-3">
                                    <input type="hidden" name="id_log" value="<?php echo htmlspecialchars($logement->getId()); ?>">
                                    <div
                                        class="grid grid-cols-2 gap-px bg-gray-200 dark:bg-gray-700 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                        <?php
                                        $reservedDates = $logementService->getReservedDates($logement->getId());
                                        $disabledDates = array_map(function ($date) {
                                            return [
                                                'from' => $date['start_date'],
                                                'to' => $date['end_date']
                                            ];
                                        }, $reservedDates);
                                        $jsonDisabledDates = htmlspecialchars(json_encode($disabledDates), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <div class="bg-white dark:bg-gray-800 p-3">
                                            <label class="block text-[9px] uppercase font-black text-gray-400 mb-1">Arrivée</label>
                                            <input type="text" name="start_date" required
                                                class="reservation-date-start w-full bg-transparent text-xs font-bold text-gray-900 dark:text-white outline-none"
                                                data-reserved="<?php echo $jsonDisabledDates; ?>" placeholder="Date">
                                        </div>
                                        <div class="bg-white dark:bg-gray-800 p-3">
                                            <label class="block text-[9px] uppercase font-black text-gray-400 mb-1">Départ</label>
                                            <input type="text" name="end_date" required
                                                class="reservation-date-end w-full bg-transparent text-xs font-bold text-gray-900 dark:text-white outline-none"
                                                data-reserved="<?php echo $jsonDisabledDates; ?>" placeholder="Date">
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-primary hover:bg-primary-dark text-white py-3.5 rounded-2xl font-black transition-all shadow-lg shadow-primary/20 active:scale-95 uppercase text-xs tracking-widest">
                                        Réserver le séjour
                                    </button>
                                </form>
                            <?php elseif (!$userRole): ?>
                                <a href="/login"
                                    class="block text-center w-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-white py-3 rounded-2xl font-bold hover:bg-primary hover:text-white transition-all text-sm">
                                    Connectez-vous pour réserver
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    :root {
        --primary: #384cffff;
        --primary-dark: #2a39cc;
    }

    .bg-primary {
        background-color: var(--primary);
    }

    .text-primary {
        color: var(--primary);
    }

    .border-primary {
        border-color: var(--primary);
    }

    .hover\:bg-primary-dark:hover {
        background-color: var(--primary-dark);
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.5;
    }

    .dark input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .airbnb-img-carousel img {
        display: none;
    }

    .airbnb-img-carousel img.active {
        display: block;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.airbnb-img-carousel').forEach(carousel => {
            let images = carousel.querySelectorAll('img');
            if (images.length < 2) return;

            let currentIndex = 0;

            let updateCarousel = (newIndex) => {
                images[currentIndex].classList.remove('active');
                images[currentIndex].style.display = 'none';

                currentIndex = newIndex;

                images[currentIndex].classList.add('active');
                images[currentIndex].style.display = 'block';
            };

            carousel.querySelector('.airbnb-carousel-btn.left')?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                let index = (currentIndex - 1 + images.length) % images.length;
                updateCarousel(index);
            });

            carousel.querySelector('.airbnb-carousel-btn.right')?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                let index = (currentIndex + 1) % images.length;
                updateCarousel(index);
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        let commonConfig = {
            dateFormat: "Y-m-d",
            minDate: "today",
            disableMobile: "true"
        };

        document.querySelectorAll('.reservation-date-start').forEach(input => {
            let reserved = JSON.parse(input.dataset.reserved || '[]');

            flatpickr(input, {
                ...commonConfig,
                disable: reserved,
                onChange: function (se lectedDates, dateStr, instance) {
                    let form = input.closest('form');
                    let endInput = form.querySelector('.reservation-date-end');
                    if (endInput && endInput._flatpickr) {
                        endInput._flatpickr.set('minDate', dateStr);
                    }
                }
            });
        });

        document.querySelectorAll('.reservation-date-end').forEach(input => {
            let reserved = JSON.parse(input.dataset.reserved || '[]');

            flatpickr(input, {
                ...commonConfig,
                disable: reserved
            });
        });
    });
</script>


<?php
require_once 'partials/script.php';
require_once 'partials/footer.php';
?>