<!-- models/edit_item_modal.php -->
<!-- Modal (Pop-up) untuk formulir edit pakaian -->
<div id="editItemModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Pakaian</h2>
        <form action="admin_kelola_pakaian.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_action" value="edit_item">
            <input type="hidden" id="edit_item_id" name="item_id">
            <input type="hidden" id="edit_current_image_url" name="current_image_url">

            <div class="mb-4">
                <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nama Pakaian:</label>
                <input type="text" id="edit_name" name="name" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                <textarea id="edit_description" name="description" rows="3" class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_size" class="block text-gray-700 text-sm font-bold mb-2">Ukuran:</label>
                    <select id="edit_size" name="size" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Ukuran</option>
                        <option value="anak-anak">Anak-anak</option>
                        <option value="dewasa">Dewasa</option>
                    </select>
                </div>
                <!-- Kolom Warna dihapus -->
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_rental_price_per_day" class="block text-gray-700 text-sm font-bold mb-2">Harga Sewa/Hari:</label>
                    <input type="number" step="0.01" id="edit_rental_price_per_day" name="rental_price_per_day" value="" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_stock" class="block text-gray-700 text-sm font-bold mb-2">Stok:</label>
                    <input type="number" id="edit_stock" name="stock" value="" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mb-6">
                <label for="edit_image" class="block text-gray-700 text-sm font-bold mb-2">Gambar Pakaian (Opsional, untuk diubah):</label>
                <input type="file" id="edit_image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Max 2MB.</p>
                
                <!-- Container untuk pratinjau gambar saat ini -->
                <div id="currentImageContainer" class="mt-2" style="display: none;">
                    <p class="text-sm text-gray-600">Gambar saat ini:</p>
                    <img id="edit_current_image_display" src="" alt="[Gambar Saat Ini]" class="mt-2 w-32 h-32 object-cover rounded-md shadow-sm">
                </div>

                <!-- Container untuk pratinjau gambar BARU yang diunggah -->
                <div id="edit_new_image_preview_container" class="mt-2" style="display: none;">
                    <p class="text-sm text-gray-600">Pratinjau Gambar Baru:</p>
                    <img id="edit_new_image_preview" src="" alt="[Pratinjau Gambar Baru]" class="mt-2 w-32 h-32 object-cover rounded-md shadow-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditItemModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-200">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md">
                    Perbarui Item
                </button>
            </div>
        </form>
    </div>
</div>
