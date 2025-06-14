<div id="addUserModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Pengguna Baru</h2>
        <form action="admin_kelola_pengguna.php" method="POST">
            <input type="hidden" name="form_action" value="add_user">
            
            <div class="mb-4">
                <label for="add_username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" id="add_username" name="username" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="add_email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="add_email" name="email" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="add_password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="add_password" name="password" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="add_role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                <select id="add_role" name="role" class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddUserModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-200">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md">
                    Tambah Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
