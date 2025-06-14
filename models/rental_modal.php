<!-- models/rental_modal.php -->
<!-- Modal (Pop-up) untuk form sewa pakaian -->
<div id="rentalModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-6 text-center">Sewa [Nama Pakaian]</h2>
        <form action="home.php" method="POST"> <!-- Aksi diarahkan ke home.php karena di sana logika sewa diproses -->
            <input type="hidden" name="action" value="rent_item">
            <input type="hidden" id="modalItemId" name="item_id">
            <div class="mb-4">
                <label for="rental_quantity" class="block text-gray-700 text-sm font-bold mb-2">Kuantitas:</label>
                <!-- Input Kuantitas -->
                <input type="number" id="rental_quantity" name="quantity" min="1" value="1" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="rental_quantity_info" class="text-xs text-gray-500 mt-1">Stok tersedia: --</p>
            </div>
            <div class="mb-4">
                <label for="rental_date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Sewa:</label>
                <input type="date" id="rental_date" name="rental_date" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="return_date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Kembali (Estimasi):</label>
                <input type="date" id="return_date" name="return_date" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRentalModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-200">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md">
                    Sewa
                </button>
            </div>
        </form>
    </div>
</div>
