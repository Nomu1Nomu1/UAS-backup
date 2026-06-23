<?php
// Pastikan hanya menerima request POST demi keamanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lakukan type casting (int) agar string dari form berubah jadi angka bulat
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        // Panggil fungsi delete dari userModel yang sudah mengarah ke 'user_id'
        if ($this->userModel->delete($id)) {
            $_SESSION['flash'] = "User berhasil dihapus.";
        } else {
            $_SESSION['flash'] = "Gagal menghapus user. Data mungkin sedang digunakan di tabel lain.";
        }
    } else {
        $_SESSION['flash'] = "ID User tidak valid!";
    }
}

// Kembalikan ke halaman daftar 
header("Location: /?page=user&action=index");
exit;
?>