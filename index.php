<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuaraKita | Sistem Pengaduan & Aspirasi Mahasiswa</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="assets/js/tailwindcss.js"></script>
    <script>window.tailwind || document.write('<script src="https://cdn.tailwindcss.com"><\/script>')</script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        navy: {
                            800: '#0f172a',
                            900: '#020617',
                        },
                        tealBrand: '#0f766e',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'pulse-glow': 'pulseGlow 3s infinite',
                        'shimmer': 'shimmer 2.5s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-15px)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                        },
                        shimmer: {
                            '100%': { transform: 'translateX(100%)' }
                        }
                    }
                },
            },
        }
    </script>
    
    <style>
        /* Modern Glassmorphism & UI Touches */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background: linear-gradient(135deg, #4ade80 0%, #10b981 50%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gradient-border {
            position: relative;
            background-clip: padding-box;
        }
        .gradient-border::after {
            content: '';
            position: absolute;
            top: -1px; right: -1px; bottom: -1px; left: -1px;
            background: linear-gradient(135deg, rgba(34,197,94,0.4), rgba(15,118,110,0.1));
            z-index: -1;
            border-radius: inherit;
        }
        /* Mouse Parallax Smoothness */
        .tilt-card {
            transition: transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }
    </style>
</head>
<body id="beranda" class="bg-slate-900 text-slate-100 font-sans antialiased overflow-x-hidden selection:bg-brand-500 selection:text-white">

    <!-- Header / Navbar -->
    <?php include 'partials/header.php'; ?>

    <main>
        <!-- ================= HERO SECTION ================= -->
        <section class="relative min-h-screen pt-32 pb-20 flex items-center justify-center overflow-hidden">
            <!-- Background Image with Dark Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="assets/img/123.jpg" alt="Kampus Background" class="w-full h-full object-cover opacity-20 filter blur-[2px]">
                <div class="absolute inset-0 bg-gradient-to-b from-navy-900/90 via-navy-900/95 to-slate-900"></div>
            </div>

            <!-- Glowing Animated Blobs -->
            <div class="absolute top-1/4 left-10 w-96 h-96 bg-brand-500/20 rounded-full blur-[120px] pointer-events-none animate-float"></div>
            <div class="absolute bottom-1/3 right-10 w-96 h-96 bg-tealBrand/30 rounded-full blur-[140px] pointer-events-none animate-float-delayed"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Left Content -->
                <div class="lg:col-span-7 text-center lg:text-left" data-aos="fade-right" data-aos-duration="1000">
                    
                    <!-- Pill Tag -->
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full glass-dark border border-brand-500/30 text-brand-300 text-xs md:text-sm font-medium mb-6 shadow-lg backdrop-blur-md">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                        </span>
                        <span>Sistem Pengaduan Digital Kampus Integratif</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15] text-white">
                        Suarakan Aspirasi, <br />
                        <span class="gradient-text">Bangun Kampus Masa Depan.</span>
                    </h1>

                    <!-- Description -->
                    <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Platform aman, transparan, dan terenkripsi bagi mahasiswa untuk menyampaikan aspirasi, laporan, dan masukan demi menciptakan transparansi serta kemajuan civitas akademika.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="auth/login.php" class="relative group overflow-hidden rounded-xl p-[1px] focus:outline-none w-full sm:w-auto">
                            <span class="absolute inset-0 bg-gradient-to-r from-brand-500 via-tealBrand to-brand-400 rounded-xl animate-shimmer"></span>
                            <span class="relative block px-8 py-4 bg-slate-900 rounded-[11px] text-white font-semibold group-hover:bg-transparent transition-all duration-300 text-center">
                                <i class="fa-solid fa-paper-plane mr-2 text-brand-400 group-hover:text-white"></i> Mulai Lapor Sekarang
                            </span>
                        </a>

                        <a href="#fitur" class="px-8 py-4 rounded-xl glass-dark text-slate-200 font-semibold border border-slate-700/60 hover:bg-white/10 hover:border-slate-500 transition-all duration-300 w-full sm:w-auto text-center">
                            Pelajari Fitur <i class="fa-solid fa-arrow-down text-xs ml-2 text-brand-400"></i>
                        </a>
                    </div>
                </div>

                <!-- Hero Right Interactive Card (Parallax Effect) -->
                <div class="lg:col-span-5" data-aos="fade-left" data-aos-duration="1000">
                    <div id="heroCard" class="tilt-card glass-dark p-8 rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden group">
                        
                        <!-- Floating Decorative Badge -->
                        <div class="absolute -top-12 -right-12 w-40 h-40 bg-brand-500/10 rounded-full blur-xl group-hover:bg-brand-500/20 transition-all"></div>
                        
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-brand-500/20 flex items-center justify-center text-brand-400">
                                    <i class="fa-solid fa-shield-halved text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">Keamanan Terjamin</h4>
                                    <p class="text-xs text-slate-400">Enkripsi Laporan End-to-End</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-brand-500/10 text-brand-400 text-xs font-semibold rounded-full border border-brand-500/20">Aktif</span>
                        </div>

                        <!-- Card Preview Item -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/50 flex items-start gap-4">
                                <div class="p-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-semibold text-slate-200">Verifikasi Berkas Instan</h5>
                                    <p class="text-xs text-slate-400 mt-1">Laporan diverifikasi oleh sistem secara otomatis dalam hitungan detik.</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/50 flex items-start gap-4">
                                <div class="p-2.5 bg-teal-500/10 text-teal-400 rounded-xl">
                                    <i class="fa-solid fa-eye"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-semibold text-slate-200">Transparansi Penanganan</h5>
                                    <p class="text-xs text-slate-400 mt-1">Lacak status penyelesaian tindak lanjut secara real-time.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-800 text-center">
                            <span class="text-xs text-slate-400">
                                <i class="fa-solid fa-users text-brand-400 mr-1"></i> Digunakan oleh ribuan mahasiswa terverifikasi.
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- ================= STATISTIK SECTION ================= -->
        <section class="relative py-16 bg-navy-800 border-y border-slate-800">
            <?php
            require_once 'config.php';

            $sekolah =$config->query("SELECT COUNT(*) AS total FROM sekolah");
            $totalSekolah = $sekolah ? $sekolah->fetch_assoc()['total'] : 0;

            $pengaduan =$config->query("SELECT COUNT(*) AS total FROM pengaduan");
            $totalPengaduan = $pengaduan ? $pengaduan->fetch_assoc()['total'] : 0;

            $pengguna =$config->query("SELECT COUNT(*) AS total FROM users ");
            $totalPengguna = $pengguna ? $pengguna->fetch_assoc()['total'] : 0;

            $ditanggapi =$config->query("SELECT COUNT(*) AS total FROM pengaduan WHERE status='selesai'");
            $totalDitanggapi = $ditanggapi ? $ditanggapi->fetch_assoc()['total'] : 0;
            $persenRespons =$totalPengaduan > 0 ? round(($totalDitanggapi / $totalPengaduan) * 100) : 0;
            ?>

            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8" data-aos="fade-up">
                    
                    <!-- Stat Card 1 -->
                    <div class="glass-dark p-6 rounded-2xl border border-slate-800 hover:border-brand-500/40 transition-all duration-300 text-center group">
                        <div class="w-12 h-12 mx-auto mb-4 bg-brand-500/10 rounded-xl flex items-center justify-center text-brand-400 group-hover:scale-110 transition duration-300">
                            <i class="fa-solid fa-graduation-cap text-xl"></i>
                        </div>
                        <div class="counter text-3xl md:text-4xl font-extrabold text-white mb-1" data-target="<?= $totalSekolah ?>">0</div>
                        <div class="text-xs md:text-sm font-medium text-slate-400">Kampus Terintegrasi</div>
                    </div>

                    <!-- Stat Card 2 -->
                    <div class="glass-dark p-6 rounded-2xl border border-slate-800 hover:border-brand-500/40 transition-all duration-300 text-center group">
                        <div class="w-12 h-12 mx-auto mb-4 bg-brand-500/10 rounded-xl flex items-center justify-center text-brand-400 group-hover:scale-110 transition duration-300">
                            <i class="fa-solid fa-inbox text-xl"></i>
                        </div>
                        <div class="counter text-3xl md:text-4xl font-extrabold text-white mb-1" data-target="<?= $totalPengaduan ?>">0</div>
                        <div class="text-xs md:text-sm font-medium text-slate-400">Laporan Diterima</div>
                    </div>

                    <!-- Stat Card 3 -->
                    <div class="glass-dark p-6 rounded-2xl border border-slate-800 hover:border-brand-500/40 transition-all duration-300 text-center group">
                        <div class="w-12 h-12 mx-auto mb-4 bg-brand-500/10 rounded-xl flex items-center justify-center text-brand-400 group-hover:scale-110 transition duration-300">
                            <i class="fa-solid fa-user-shield text-xl"></i>
                        </div>
                        <div class="counter text-3xl md:text-4xl font-extrabold text-white mb-1" data-target="<?= $totalPengguna ?>">0</div>
                        <div class="text-xs md:text-sm font-medium text-slate-400">Pengguna Aktif</div>
                    </div>

                    <!-- Stat Card 4 -->
                    <div class="glass-dark p-6 rounded-2xl border border-slate-800 hover:border-brand-500/40 transition-all duration-300 text-center group">
                        <div class="w-12 h-12 mx-auto mb-4 bg-brand-500/10 rounded-xl flex items-center justify-center text-brand-400 group-hover:scale-110 transition duration-300">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <div class="text-3xl md:text-4xl font-extrabold text-white mb-1">
                            <span class="counter" data-target="<?= $persenRespons ?>">0</span>%
                        </div>
                        <div class="text-xs md:text-sm font-medium text-slate-400">Tingkat Penanganan</div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= TENTANG SECTION ================= -->
        <section id="tentang" class="py-24 bg-slate-900 relative">
            <div class="max-w-7xl mx-auto px-6">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-brand-400 text-xs font-bold tracking-widest uppercase">Mengenal SuaraKita</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-2">Satu Wadah untuk Perubahan Positif</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    
                    <div class="lg:col-span-7 space-y-6" data-aos="fade-right">
                        <div class="p-8 rounded-3xl bg-slate-800/40 border border-slate-800 hover:border-brand-500/30 transition-all duration-300">
                            <h3 class="text-xl font-bold text-white mb-3 flex items-center gap-3">
                                <i class="fa-solid fa-lock text-brand-400"></i> Ruang Aman Bersuara
                            </h3>
                            <p class="text-slate-300 text-sm leading-relaxed">
                                SuaraKita dirancang khusus untuk memastikan kerahasiaan pelapor terjamin, terhindar dari intimidasi, serta memungkinkan pengaduan yang terstruktur tanpa hambatan birokrasi.
                            </p>
                        </div>

                        <div class="p-8 rounded-3xl bg-slate-800/40 border border-slate-800 hover:border-brand-500/30 transition-all duration-300">
                            <h3 class="text-xl font-bold text-white mb-3 flex items-center gap-3">
                                <i class="fa-solid fa-chart-line text-brand-400"></i> Sistem Akuntabel & Terukur
                            </h3>
                            <p class="text-slate-300 text-sm leading-relaxed">
                                Setiap pengaduan memiliki nomor tiket unik yang memungkinkan transparansi pemantauan dari status peninjauan awal hingga penyelesaian akhir.
                            </p>
                        </div>
                    </div>

                    <!-- Visi Card -->
                    <div class="lg:col-span-5" data-aos="fade-left">
                        <div class="relative p-10 rounded-3xl bg-gradient-to-br from-brand-900/60 via-slate-800 to-navy-800 border border-brand-500/30 shadow-xl overflow-hidden group">
                            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-brand-500/20 rounded-full blur-2xl group-hover:scale-125 transition duration-500"></div>
                            
                            <div class="w-16 h-16 rounded-2xl bg-brand-500/20 flex items-center justify-center text-brand-400 mb-6">
                                <i class="fa-solid fa-bullseye text-2xl"></i>
                            </div>

                            <h4 class="text-2xl font-bold text-white mb-3">Visi Kami</h4>
                            <p class="text-slate-300 text-sm leading-relaxed italic">
                                "Menjadi pendorong terwujudnya tata kelola kampus yang responsif, transparan, serta mengedepankan keadilan demi kesejahteraan seluruh mahasiswa."
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= FITUR UNGGULAN ================= -->
        <section id="fitur" class="py-24 bg-navy-800 relative">
            <div class="max-w-7xl mx-auto px-6">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-brand-400 text-xs font-bold tracking-widest uppercase">Fitur Modern</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-2">Didesain untuk Kemudahan Pengguna</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Fitur 1 -->
                    <div data-aos="fade-up" data-aos-delay="100" class="group p-8 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-brand-500/50 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-brand-500/10 text-brand-400 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            <i class="fa-solid fa-file-pen"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Sistem Pengaduan</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-6">Sampaikan keluhan dan saran lengkap dengan bukti pendukung secara terenkripsi.</p>
                        <span class="text-xs font-semibold text-brand-400 group-hover:translate-x-1 transition inline-block">Selengkapnya &rarr;</span>
                    </div>

                    <!-- Fitur 2 -->
                    <div data-aos="fade-up" data-aos-delay="200" class="group p-8 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-brand-500/50 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-brand-500/10 text-brand-400 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Dashboard Admin</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-6">Panel manajemen intuitif bagi pengelola kampus untuk tindak lanjut cepat.</p>
                        <span class="text-xs font-semibold text-brand-400 group-hover:translate-x-1 transition inline-block">Selengkapnya &rarr;</span>
                    </div>

                    <!-- Fitur 3 -->
                    <div data-aos="fade-up" data-aos-delay="300" class="group p-8 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-brand-500/50 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-brand-500/10 text-brand-400 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Verifikasi Identitas</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-6">Validasi akun mahasiswa aktif mencegah laporan palsu dan spam.</p>
                        <span class="text-xs font-semibold text-brand-400 group-hover:translate-x-1 transition inline-block">Selengkapnya &rarr;</span>
                    </div>

                    <!-- Fitur 4 -->
                    <div data-aos="fade-up" data-aos-delay="400" class="group p-8 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-brand-500/50 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-14 h-14 bg-brand-500/10 text-brand-400 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Komunikasi 2 Arah</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-6">Ruang diskusi terbuka antara pelapor dan pimpinan untuk klarifikasi.</p>
                        <span class="text-xs font-semibold text-brand-400 group-hover:translate-x-1 transition inline-block">Selengkapnya &rarr;</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= PANDUAN WORKFLOW ================= -->
        <section id="panduan" class="py-24 bg-slate-900">
            <div class="max-w-5xl mx-auto px-6">
                
                <div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-brand-400 text-xs font-bold tracking-widest uppercase">Langkah Mudah</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-2">Bagaimana Sistem Bekerja?</h2>
                </div>

                <div class="relative border-l-2 border-slate-800 ml-4 md:ml-32 space-y-12">
                    
                    <!-- Step 1 -->
                    <div data-aos="fade-up" class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-slate-900 border-2 border-brand-500 flex items-center justify-center text-brand-400 font-bold text-xs group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            1
                        </div>
                        <div class="glass-dark p-6 rounded-2xl border border-slate-800">
                            <h4 class="text-lg font-bold text-white mb-1">Buat Laporan</h4>
                            <p class="text-slate-400 text-xs md:text-sm leading-relaxed">
                                Masukkan detail pengaduan, pilih kategori yang sesuai, dan unggah bukti pendukung secara lengkap.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div data-aos="fade-up" data-aos-delay="100" class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-slate-900 border-2 border-brand-500 flex items-center justify-center text-brand-400 font-bold text-xs group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            2
                        </div>
                        <div class="glass-dark p-6 rounded-2xl border border-slate-800">
                            <h4 class="text-lg font-bold text-white mb-1">Proses Verifikasi</h4>
                            <p class="text-slate-400 text-xs md:text-sm leading-relaxed">
                                Tim admin memeriksa kelayakan data pengaduan sebelum diteruskan ke pihak berwenang.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div data-aos="fade-up" data-aos-delay="200" class="relative pl-8 md:pl-12 group">
                        <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-slate-900 border-2 border-brand-500 flex items-center justify-center text-brand-400 font-bold text-xs group-hover:bg-brand-500 group-hover:text-white transition duration-300">
                            3
                        </div>
                        <div class="glass-dark p-6 rounded-2xl border border-slate-800">
                            <h4 class="text-lg font-bold text-white mb-1">Tindak Lanjut & Solusi</h4>
                            <p class="text-slate-400 text-xs md:text-sm leading-relaxed">
                                Dapatkan pemberitahuan resmi dan hasil tanggapan langsung melalui riwayat akun Anda.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>

    <!-- JS Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-out-cubic'
        });

        // 3D Parallax Tilt Effect on Hero Card
        const heroCard = document.getElementById('heroCard');
        if (heroCard) {
            document.addEventListener('mousemove', (e) => {
                const xAxis = (window.innerWidth / 2 - e.pageX) / 45;
                const yAxis = (window.innerHeight / 2 - e.pageY) / 45;
                heroCard.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
            });
        }

        // Counter Animation Logic
        const counters = document.querySelectorAll('.counter');
        const startCounter = (counter) => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const speed = 150;
                const increment = Math.ceil(target / speed) || 1;

                if (count < target) {
                    counter.innerText = Math.min(count + increment, target);
                    setTimeout(updateCount, 25);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        };

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCounter(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    </script>
</body>
</html>