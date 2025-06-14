// assets/js/script.js
// File ini berisi JavaScript kustom untuk interaktivitas UI.
//
// CATATAN PENTING UNTUK STYLING:
// Perubahan pada lebar tabel, tampilan minimalis modern, warna, font,
// dan estetika keseluruhan aplikasi diatur oleh CSS.
// File utama untuk styling adalah `assets/css/style.css`.
// Silakan periksa file tersebut untuk penyesuaian visual yang Anda inginkan.

/**
 * Membuka modal penyewaan dengan detail item yang diberikan.
 * @param {number} itemId ID unik dari item pakaian.
 * @param {string} itemName Nama pakaian yang akan disewa.
 * @param {number} availableStock Stok yang tersedia untuk item ini.
 */
function openRentalModal(itemId, itemName, availableStock) {
    document.getElementById('modalItemId').value = itemId;
    document.getElementById('modalTitle').innerText = 'Sewa ' + itemName;
    
    // Set kuantitas min/max dan info stok
    const rentalQuantityInput = document.getElementById('rental_quantity');
    const rentalQuantityInfo = document.getElementById('rental_quantity_info');
    rentalQuantityInput.setAttribute('min', '1');
    rentalQuantityInput.setAttribute('max', availableStock);
    rentalQuantityInput.value = 1; // Default ke 1
    rentalQuantityInfo.textContent = `Stok tersedia: ${availableStock}`;

    // Set tanggal minimum untuk rental_date (hari ini)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('rental_date').setAttribute('min', today);
    document.getElementById('rental_date').value = today; // Atur default ke hari ini
    
    // Set tanggal minimum untuk return_date (besok)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('return_date').setAttribute('min', tomorrow.toISOString().split('T')[0]);
    document.getElementById('return_date').value = tomorrow.toISOString().split('T')[0]; // Atur default ke besok

    document.getElementById('rentalModal').classList.remove('hidden');
}

/**
 * Menutup modal penyewaan.
 */
function closeRentalModal() {
    document.getElementById('rentalModal').classList.add('hidden');
}

/**
 * Membuka modal konfirmasi pengembalian dengan detail penyewaan.
 * @param {number} rentalId ID unik dari transaksi penyewaan.
 * @param {string} itemName Nama pakaian yang akan dikembalikan.
 */
function openReturnModal(rentalId, itemName) {
    document.getElementById('returnModalRentalId').value = rentalId;
    document.getElementById('returnModalItemName').innerText = itemName;
    document.getElementById('returnModal').classList.remove('hidden');
}

/**
 * Menutup modal konfirmasi pengembalian.
 */
function closeReturnModal() {
    document.getElementById('returnModal').classList.add('hidden');
}

/**
 * Membuka modal untuk menambahkan pakaian baru.
 */
function openAddItemModal() {
    document.getElementById('addItemModal').classList.remove('hidden');
    // Opsional: reset form di dalam modal jika sudah pernah diisi
    document.getElementById('addItemModal').querySelector('form').reset();
    
    // Sembunyikan dan hapus pratinjau gambar sebelumnya
    document.getElementById('add_image_preview_container').style.display = 'none';
    document.getElementById('add_image_preview').src = '';
    document.getElementById('add_image').value = ''; // Reset input file
}

/**
 * Menutup modal untuk menambahkan pakaian baru.
 */
function closeAddItemModal() {
    document.getElementById('addItemModal').classList.add('hidden');
    // Sembunyikan dan hapus pratinjau gambar saat modal ditutup
    document.getElementById('add_image_preview_container').style.display = 'none';
    document.getElementById('add_image_preview').src = '';
    document.getElementById('add_image').value = ''; // Reset input file
}

/**
 * Membuka modal untuk mengedit pakaian.
 * @param {object} itemData Objek yang berisi semua data item pakaian yang akan diedit.
 */
function openEditItemModal(itemData) {
    // Isi formulir modal edit dengan data item
    document.getElementById('edit_item_id').value = itemData.item_id;
    document.getElementById('edit_name').value = itemData.name;
    document.getElementById('edit_description').value = itemData.description;
    document.getElementById('edit_size').value = itemData.size;
    // document.getElementById('edit_color').value = itemData.color; // Kolom warna dihapus, baris ini harus dihapus jika Anda sudah menghapusnya dari HTML
    document.getElementById('edit_rental_price_per_day').value = itemData.rental_price_per_day;
    document.getElementById('edit_stock').value = itemData.stock;

    // Tampilkan gambar saat ini jika ada
    if (itemData.image_url) {
        document.getElementById('edit_current_image_display').src = itemData.image_url;
        document.getElementById('edit_current_image_url').value = itemData.image_url; // Set hidden input
        document.getElementById('currentImageContainer').style.display = 'block';
    } else {
        document.getElementById('edit_current_image_display').src = '';
        document.getElementById('edit_current_image_url').value = '';
        document.getElementById('currentImageContainer').style.display = 'none';
    }

    // Sembunyikan pratinjau gambar baru saat modal dibuka (sebelum pengguna memilih file baru)
    document.getElementById('edit_new_image_preview_container').style.display = 'none';
    document.getElementById('edit_new_image_preview').src = '';
    document.getElementById('edit_image').value = ''; // Reset input file

    // Tampilkan modal edit
    document.getElementById('editItemModal').classList.remove('hidden');
}

/**
 * Menutup modal untuk mengedit pakaian.
 */
function closeEditItemModal() {
    document.getElementById('editItemModal').classList.add('hidden');
    // Opsional: reset form di dalam modal setelah ditutup
    document.getElementById('editItemModal').querySelector('form').reset();
    document.getElementById('currentImageContainer').style.display = 'none'; // Sembunyikan container gambar saat ini
    document.getElementById('edit_new_image_preview_container').style.display = 'none'; // Sembunyikan container gambar baru
}


/**
 * Membuka modal untuk menambahkan pengguna baru.
 */
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.getElementById('addUserModal').querySelector('form').reset();
}

/**
 * Menutup modal untuk menambahkan pengguna baru.
 */
function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}

/**
 * Membuka modal untuk mengedit pengguna.
 * @param {object} userData Objek yang berisi semua data pengguna yang akan diedit.
 * @param {number} loggedInUserId ID pengguna yang sedang login (admin saat ini).
 */
function openEditUserModal(userData, loggedInUserId) {
    document.getElementById('edit_user_id').value = userData.user_id;
    document.getElementById('edit_username').value = userData.username;
    document.getElementById('edit_email').value = userData.email;
    document.getElementById('edit_role').value = userData.role; // Set role dropdown
    document.getElementById('edit_password').value = ''; // Kosongkan password field untuk keamanan

    const editRoleSelect = document.getElementById('edit_role');
    const editRoleMessage = document.getElementById('edit_role_message');
    const roleDropdownContainer = editRoleSelect.closest('div.mb-6'); // Dapatkan container div Role

    // Reset tampilan dan status disabled/display terlebih dahulu untuk semua skenario
    editRoleSelect.disabled = false;
    if (roleDropdownContainer) {
        roleDropdownContainer.style.display = 'block'; // Pastikan container dropdown terlihat default
    }
    for (let i = 0; i < editRoleSelect.options.length; i++) {
        editRoleSelect.options[i].disabled = false; 
        editRoleSelect.options[i].style.display = 'block'; 
    }
    editRoleMessage.textContent = "";
    editRoleMessage.style.display = 'none';

    // Scenario 1: Admin sedang mengedit akunnya sendiri
    if (userData.user_id == loggedInUserId) {
        editRoleSelect.disabled = true;
        editRoleMessage.textContent = "Anda tidak dapat mengubah peran akun admin Anda sendiri.";
        editRoleMessage.style.display = 'block';
    } 
    // Scenario 2: Admin sedang mengedit pengguna lain yang saat ini adalah 'customer'
    // Sembunyikan opsi 'admin' di dropdown role
    else if (userData.role === 'customer') {
        for (let i = 0; i < editRoleSelect.options.length; i++) {
            if (editRoleSelect.options[i].value === 'admin') {
                editRoleSelect.options[i].disabled = true; // Nonaktifkan juga
                editRoleSelect.options[i].style.display = 'none'; // Sembunyikan
            } else {
                editRoleSelect.options[i].disabled = false; // Pastikan opsi lain aktif
                editRoleSelect.options[i].style.display = 'block'; // Pastikan opsi lain terlihat
            }
        }
        editRoleMessage.textContent = "Admin tidak dapat mengubah peran pengguna 'customer' menjadi 'admin'. Peran akan tetap 'Customer'.";
        editRoleMessage.style.display = 'block';
        editRoleSelect.value = 'customer'; // Pastikan nilai dropdown tetap 'customer'
    } else { // Scenario 3: Admin mengedit pengguna lain yang sudah admin
        // Dalam skenario ini, admin boleh mengubah peran admin lain menjadi customer jika diinginkan
        editRoleSelect.disabled = false;
        for (let i = 0; i < editRoleSelect.options.length; i++) {
            if (editRoleSelect.options[i].value === 'admin') {
                editRoleSelect.options[i].disabled = false;
                editRoleSelect.options[i].style.display = 'block';
            }
        }
        editRoleMessage.textContent = "";
        editRoleMessage.style.display = 'none';
    }

    document.getElementById('editUserModal').classList.remove('hidden');
}

/**
 * Menutup modal untuk mengedit pengguna.
 */
function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserModal').querySelector('form').reset();
    
    // Pastikan dropdown role dan semua opsi diaktifkan kembali dan ditampilkan saat modal ditutup
    const editRoleSelect = document.getElementById('edit_role');
    const roleDropdownContainer = editRoleSelect.closest('div.mb-6'); 

    editRoleSelect.disabled = false;
    if (roleDropdownContainer) {
        roleDropdownContainer.style.display = 'block'; 
    }
    for (let i = 0; i < editRoleSelect.options.length; i++) {
        editRoleSelect.options[i].disabled = false; 
        editRoleSelect.options[i].style.display = 'block'; 
    }
    // Sembunyikan pesan alasan role dinonaktifkan
    document.getElementById('edit_role_message').style.display = 'none';
    document.getElementById('edit_role_message').textContent = '';
}


// --- Event Listener untuk Pratinjau Gambar (Tambah Item) ---
document.addEventListener('DOMContentLoaded', () => {
    const addImageInput = document.getElementById('add_image');
    const addImagePreview = document.getElementById('add_image_preview');
    const addImagePreviewContainer = document.getElementById('add_image_preview_container');

    if (addImageInput && addImagePreview && addImagePreviewContainer) {
        addImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    addImagePreview.src = e.target.result;
                    addImagePreviewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                addImagePreview.src = '';
                addImagePreviewContainer.style.display = 'none';
            }
        });
    }

    // --- Event Listener untuk Pratinjau Gambar BARU (Edit Item) ---
    const editImageInput = document.getElementById('edit_image');
    const editNewImagePreview = document.getElementById('edit_new_image_preview');
    const editNewImagePreviewContainer = document.getElementById('edit_new_image_preview_container');
    const editCurrentImageContainer = document.getElementById('currentImageContainer'); // Dapatkan container gambar saat ini

    if (editImageInput && editNewImagePreview && editNewImagePreviewContainer && editCurrentImageContainer) {
        editImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editNewImagePreview.src = e.target.result;
                    editNewImagePreviewContainer.style.display = 'block';
                    editCurrentImageContainer.style.display = 'none'; // Sembunyikan gambar saat ini jika ada pratinjau baru
                };
                reader.readAsDataURL(file);
            } else {
                editNewImagePreview.src = '';
                editNewImagePreviewContainer.style.display = 'none';
                // Tampilkan kembali gambar saat ini jika input file dikosongkan DAN ada gambar sebelumnya
                if (document.getElementById('edit_current_image_url').value) {
                    editCurrentImageContainer.style.display = 'block';
                }
            }
        });
    }


    // Mengatur listener untuk menutup kotak pesan otomatis setelah beberapa detik.
    const messageBox = document.getElementById('messageBox');
    if (messageBox) {
        // Atur timeout untuk menyembunyikan kotak pesan setelah 5 detik
        setTimeout(() => {
            messageBox.style.display = 'none';
        }, 5000); // Pesan akan hilang setelah 5 detik (5000 milidetik)
    }
});
