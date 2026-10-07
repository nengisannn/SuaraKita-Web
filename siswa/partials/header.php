<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/notification.php';

$siswa_id = $_SESSION['siswa_id'] ?? 0;
$siswa = db_fetch_one(
    "SELECT s.*, se.nama_sekolah 
     FROM users s
     LEFT JOIN sekolah se ON s.sekolah_id = se.id
     WHERE s.id = ? AND s.level = 'siswa'",
    [$siswa_id],
    "i"
);

$unread_count = count_unread_notifications($siswa_id);
$notif_list   = get_unread_notifications($siswa_id, 5);
?>

<header class="fixed top-0 left-64 right-0 h-20 bg-white shadow-sm flex items-center px-6 z-40 transition-all border-b border-slate-200">

    <!-- Judul & Info Kampus/Sekolah -->
    <div class="flex-1">
        <h1 class="text-lg font-bold text-navy">SuaraKita Portal</h1>
        <p class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($siswa['nama_sekolah'] ?? 'Sistem Pengaduan') ?></p>
    </div>

    <!-- Notification Bell & User Info -->
    <div class="flex items-center gap-6">

        <!-- Lonceng Notifikasi Dropdown -->
        <div class="relative">
            <button id="notifBellBtn" onclick="toggleNotifDropdown()" class="relative p-2 rounded-full text-slate-500 hover:text-navy hover:bg-slate-100 transition focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <?php if ($unread_count > 0): ?>
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </button>

            <!-- Dropdown Card -->
            <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-slate-200 z-50 overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-700">🔔 Notifikasi (<?= $unread_count ?> Baru)</span>
                </div>
                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto text-xs">
                    <?php if (empty($notif_list)): ?>
                        <div class="p-4 text-center text-slate-400 italic">Belum ada notifikasi baru.</div>
                    <?php else: ?>
                        <?php foreach ($notif_list as $n): ?>
                            <a href="detail_pengaduan.php?id=<?= $n['pengaduan_id'] ?>" class="block p-3 hover:bg-slate-50 transition <?= $n['is_read'] ? 'opacity-60' : 'bg-teal/5 font-medium' ?>">
                                <div class="font-bold text-navy mb-0.5"><?= htmlspecialchars($n['judul']) ?></div>
                                <div class="text-slate-600 line-clamp-2"><?= htmlspecialchars($n['pesan']) ?></div>
                                <div class="text-[10px] text-slate-400 mt-1"><?= date('d M H:i', strtotime($n['created_at'])) ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="flex items-center gap-3 border-l border-slate-200 pl-4">
            <div class="text-right">
                <p class="text-sm font-semibold text-navy">
                    <?= htmlspecialchars($_SESSION['siswa_username'] ?? 'Mahasiswa'); ?>
                </p>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($siswa['kelas'] ?? 'Mahasiswa') ?></p>
            </div>

            <!-- Profil Siswa -->
            <a href="profil_siswa.php">
                <img src="../assets/img/<?= !empty($siswa['foto']) ? htmlspecialchars($siswa['foto']) : 'default.png'; ?>" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-sm">
            </a>
        </div>
    </div>

</header>

<script>
function toggleNotifDropdown() {
    const dd = document.getElementById('notifDropdown');
    dd.classList.toggle('hidden');
}
</script>
