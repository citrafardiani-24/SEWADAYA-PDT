<!-- models/return_modal.php -->
<!-- Modal (Pop-up) untuk konfirmasi pengembalian pakaian -->
<div id="returnModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 id="returnModalTitle" class="text-2xl font-bold text-gray-800 mb-6 text-center">Konfirmasi Pengembalian</h2>
        <p class="text-gray-700 mb-4">Anda akan mengembalikan item <span id="returnModalItemName" class="font-semibold"></span>?</p>
        <form action="history.php" method="POST"> <!-- Aksi diarahkan ke history.php karena di sana logika kembali diproses -->
            <input type="hidden" name="action" value="return_item">
            <input type="hidden" id="returnModalRentalId" name="rental_id">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeReturnModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-200">
                    Batal
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-200 shadow-md">
                    Konfirmasi Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>
