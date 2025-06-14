<!-- models/edit_user_modal.php -->
<!-- Modal (Pop-up) untuk formulir edit pengguna -->
<div id="editUserModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Pengguna</h2>
        <form action="admin_kelola_pengguna.php" method="POST">
            <input type="hidden" name="form_action" value="edit_user">
            <input type="hidden" id="edit_user_id" name="user_id">
            
            <div class="mb-4">
                <label for="edit_username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" id="edit_username" name="username" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="edit_email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="edit_email" name="email" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="edit_password" class="block text-gray-700 text-sm font-bold mb-2">Password (Kosongkan jika tidak diubah):</label>
                <input type="password" id="edit_password" name="password" class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="edit_role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                <select id="edit_role" name="role" class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
                <!-- Elemen untuk menampilkan pesan alasan role dinonaktifkan -->
                <p id="edit_role_message" class="text-xs text-red-500 mt-1" style="display: none;"></p>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditUserModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-200">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md">
                    Perbarui Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
