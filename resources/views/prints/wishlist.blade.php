<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Print Wishlist</title>
    @vite('resources/css/app.css')
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-800 p-8 max-w-4xl mx-auto">
    <!-- Main Container with Shadow and Rounded Corners -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <!-- Header -->
        <header class="mb-8 pb-4 border-b">
            <h1 class="text-4xl font-extrabold text-center">{{ $wishlist->name }}'s List</h1>

            <div class="flex justify-center gap-6 mt-3 text-gray-600 text-sm">
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span>{{ $wishlist->items->count() }} Items</span>
                </div>

                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>{{ date('F j, Y') }}</span>
                </div>
            </div>
        </header>

        <!-- Table with Shadow and Rounded Corners -->
        <div class="border rounded-lg shadow-md overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="w-16 p-3 text-left text-gray-600 font-medium border-b">No.</th>
                        <th class="p-3 text-left text-gray-600 font-medium border-b">Item</th>
                        <th class="w-32 p-3 text-right text-gray-600 font-medium border-b">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($wishlist->items as $index => $item)
                        <tr class="border-b">
                            <td class="p-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="p-3 font-medium">{{ $item->name }}</td>
                            <td class="p-3 text-right font-medium">
                                {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-gray-500 italic">
                                No items in this wishlist.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($wishlist->items->count())
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td colspan="2" class="p-3 text-right">Total Price</td>
                            <td class="p-3 text-right">
                                {{ number_format($wishlist->items->sum('price'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Notes with Shadow and Rounded Corners -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg shadow-sm border">
            <h2 class="text-base font-medium text-gray-700 mb-2">Notes</h2>
            <p class="text-gray-600 text-sm">
                {{ $wishlist->notes ?? 'No additional notes for this wishlist.' }}
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-500">
            Generated on {{ date('F j, Y \a\t g:i A') }}
        </div>
    </div>

    <!-- Print Button with Shadow and Rounded Corners -->
    <div class="no-print mt-6 flex justify-center">
        <button onclick="window.print()"
            class="px-6 py-2 bg-gray-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            Print Wishlist
        </button>
    </div>

    <script>
        window.onload = () => {
            window.print();
        }
    </script>
</body>

</html>
