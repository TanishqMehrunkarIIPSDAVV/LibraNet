<?php
loadComponent("Head");
loadComponent("Sidebar", [
    "components" => [
        "Home" => [
            "url" => "/incharge-dashboard",
            "icon" => "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
        ],
        "Transactions" => [
            "url" => "/incharge-transactions",
            "icon" => "M17 9V7a4 4 0 00-8 0v2M5 10h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z"
        ],
        "Profile" => [
            "url" => "/incharge-profile",
            "icon" => "M5.121 17.804A4 4 0 0112 14a4 4 0 016.879 3.804M12 14a4 4 0 100-8 4 4 0 000 8z"
        ],
        "Manipulation" => [
            "url" => "/incharge-manipulation",
            "icon" => "M4 19.5V5a2 2 0 012-2h10a2 2 0 012 2v14.5M16 2v16m-4-4l-4 4m0-4l4 4"
        ],
        "Search" => [
            "url" => "/incharge-search",
            "icon" => "M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
        ],
        "Sign Out" => [
            "url" => "/incharge-signout",
            "icon" => "M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V4a3 3 0 00-6 0v1"
        ]
    ]
]);
?>

<div class="flex-1 bg-white dark:bg-[#090A0C] text-gray-900 dark:text-white ml-16">
    <header class="bg-gray-100 dark:bg-[#101623] shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-book-reader text-gray-700 dark:text-gray-300 text-2xl"></i>
                <span class="ml-2 text-xl font-semibold text-gray-700 dark:text-gray-300">LibraNet</span>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-300 mb-6">Transactions</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <form action="/issue-book" method="POST" class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-300">Issue a Book</h2>
                <input type="text" name="member_id" placeholder="Member ID" class="w-full mb-2 p-2 rounded border focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <input type="text" name="book_id" placeholder="Book ID" class="w-full mb-2 p-2 rounded border focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full hover:bg-blue-600">Issue</button>
            </form>

            <form action="/return-book" method="POST" class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-300">Return a Book</h2>
                <input type="text" name="member_id" placeholder="Member ID" class="w-full mb-2 p-2 rounded border focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <input type="text" name="book_id" placeholder="Book ID" class="w-full mb-2 p-2 rounded border focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full hover:bg-green-700">
                    Return
                </button>
            </form>
        </div>

        <div class="mt-8 bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-300">Transaction History</h2>
            <table class="w-full border-collapse text-gray-800 dark:text-white border border-black dark:border-gray-600">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="border border-black dark:border-gray-600 p-2">Transaction ID</th>
                        <th class="border border-black dark:border-gray-600 p-2">Member ID</th>
                        <th class="border border-black dark:border-gray-600 p-2">Book ID</th>
                        <th class="border border-black dark:border-gray-600 p-2">Type</th>
                        <th class="border border-black dark:border-gray-600 p-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $transactions = [];
                    foreach ($transactions as $transaction) {
                        echo "<tr class='hover:bg-gray-100 dark:hover:bg-gray-600'>
                    <td class='border border-black dark:border-gray-600 p-2'>{$transaction['id']}</td>
                    <td class='border border-black dark:border-gray-600 p-2'>{$transaction['member_id']}</td>
                    <td class='border border-black dark:border-gray-600 p-2'>{$transaction['book_id']}</td>
                    <td class='border border-black dark:border-gray-600 p-2'>{$transaction['type']}</td>
                    <td class='border border-black dark:border-gray-600 p-2'>{$transaction['date']}</td>
                </tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </main>
</div>
<?= loadComponent("Tail"); ?>