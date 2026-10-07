<?php
// includes/notification.php - Modul Helper Notifikasi & WhatsApp/Email Gateway Simulator

if (!defined('CONFIG_LOADED')) {
    require_once __DIR__ . '/../config.php';
    define('CONFIG_LOADED', true);
}
require_once __DIR__ . '/db.php';

/**
 * Buat notifikasi baru untuk user (In-App)
 */
function create_notification($user_id, $pengaduan_id, $judul, $pesan) {
    if (!$user_id || !$pengaduan_id) return false;
    
    // Simpan ke tabel database
    $inserted = db_execute(
        "INSERT INTO notifikasi (user_id, pengaduan_id, judul, pesan, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())",
        [$user_id, $pengaduan_id, $judul, $pesan],
        "iiss"
    );

    // Simulasi Pengiriman ke WhatsApp Gateway / Email Log
    send_external_notification($user_id, $judul, $pesan);

    return $inserted;
}

/**
 * Ambil daftar notifikasi belum dibaca untuk user tertentu
 */
function get_unread_notifications($user_id, $limit = 5) {
    if (!$user_id) return [];
    return db_fetch_all(
        "SELECT * FROM notifikasi WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
        [$user_id, $limit],
        "ii"
    );
}

/**
 * Hitung jumlah notifikasi belum dibaca
 */
function count_unread_notifications($user_id) {
    if (!$user_id) return 0;
    $row = db_fetch_one(
        "SELECT COUNT(*) as unread FROM notifikasi WHERE user_id = ? AND is_read = 0",
        [$user_id],
        "i"
    );
    return $row['unread'] ?? 0;
}

/**
 * Tandai semua notifikasi milik user sebagai dibaca
 */
function mark_notifications_as_read($user_id) {
    if (!$user_id) return false;
    return db_execute(
        "UPDATE notifikasi SET is_read = 1 WHERE user_id = ?",
        [$user_id],
        "i"
    );
}

/**
 * Helper simulasi pengiriman notifikasi eksternal (WhatsApp / Email API Webhook Ready)
 */
function send_external_notification($user_id, $judul, $pesan) {
    // Log aktivitas notifikasi eksternal
    $log_message = sprintf(
        "[%s] NOTIF_SENT to User ID #%d | Judul: %s | Pesan: %s\n",
        date('Y-m-d H:i:s'),
        $user_id,
        $judul,
        $pesan
    );
    @file_put_contents(__DIR__ . '/../assets/uploads/notifications.log', $log_message, FILE_APPEND);
}
