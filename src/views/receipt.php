<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Réservation - KARI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .shadow-lg {
                box-shadow: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl w-full">
        <div class="flex justify-between items-start border-b pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">KARI</h1>
                <p class="text-gray-500">Plateforme de Location</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-semibold text-gray-700">REÇU DE RÉSERVATION</h2>
                <p class="text-gray-500">#
                    <?= htmlspecialchars($reservation['id'] ?? 'N/A') ?>
                </p>
                <p class="text-sm text-gray-400">Date:
                    <?= date('d/m/Y') ?>
                </p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-gray-600 font-semibold mb-2">Voyageur</h3>
            <p class="font-bold text-lg">
                <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
            </p>
            <p class="text-gray-600">
                <?= htmlspecialchars($user['email']) ?>
            </p>
        </div>

        <table class="w-full mb-8">
            <thead>
                <tr class="bg-gray-50 border-y">
                    <th class="py-3 text-left">Description</th>
                    <th class="py-3 text-right">Détails</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <tr>
                    <td class="py-4">
                        <p class="font-bold">Logement</p>
                        <p class="text-sm text-gray-500">
                            <?= htmlspecialchars($logement->getAddress()) ?>
                        </p>
                    </td>
                    <td class="py-4 text-right">
                        <?= htmlspecialchars($logement->getPrice()) ?> € / nuit
                    </td>
                </tr>
                <tr>
                    <td class="py-4">
                        <p class="font-bold">Dates</p>
                        <p class="text-sm text-gray-500">Du
                            <?= htmlspecialchars($reservation['start_date']) ?> au
                            <?= htmlspecialchars($reservation['end_date']) ?>
                        </p>
                    </td>
                    <td class="py-4 text-right">
                        <?php
                        $start = new DateTime($reservation['start_date']);
                        $end = new DateTime($reservation['end_date']);
                        $days = $end->diff($start)->days;
                        echo $days . " nuits";
                        ?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-200">
                    <td class="py-4 font-bold text-xl">Total Payé</td>
                    <td class="py-4 text-right font-bold text-xl text-blue-600">
                        <?= $days * $logement->getPrice() ?> €
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="border-t pt-6 text-center text-gray-500 text-sm">
            <p>Merci pour votre confiance !</p>
            <p>KARI Inc. - Support: contact@kari.com</p>
        </div>

        <div class="mt-8 text-center no-print">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Télécharger le Reçu (PDF)
            </button>
            <a href="/reservations" class="ml-4 text-gray-600 hover:underline">Retour</a>
        </div>
    </div>

</body>

</html>