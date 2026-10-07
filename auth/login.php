<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SuaraKita</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html, body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
    </style>
    <script src="../assets/js/tailwindcss.js"></script>
    <script>window.tailwind || document.write('<script src="https://cdn.tailwindcss.com"><\/script>')</script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mint: '#DDF4E7',
                        green: '#67C090',
                        teal: '#26667F',
                        navy: '#124170',
                    },
                },
            },
        }
    </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-t from-mint to-violet-50 px-4">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-4">
        <img src="../assets/img/logoSuaraKita.png"
             alt="Logo SuaraKita"
             class="h-24 mx-auto rounded-3xl object-cover mb-2">
    </div>

    <!-- Card -->
    <div class="bg-white p-8 rounded-3xl shadow-lg border-teal">

        <form method="post" action="../aksi/aksi_login.php" class="space-y-6">

            <p class="text-gray-600 text-center mb-6">
            Masuk ke akun <span class=" font-semibold text-navy">SuaraKita</span>
            </p>

            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">
                    Username
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A9.001 9.001 0 0112 15c2.21 0 4.21.805 5.879 2.146M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="username" required
                           placeholder="Masukkan username"
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-teal focus:outline-none transition">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">
                    Password
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z"/>
                        </svg>
                    </span>

                    <input type="password" id="password" name="password" required
                           placeholder="Masukkan password"
                           class="w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-lg
                                  focus:border-teal focus:outline-none transition">

                    <!-- Toggle -->
                    <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-teal transition">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                     -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Button -->
            <button type="submit"
                    class="w-full bg-gradient-to-br from-green to-teal text-white py-3 rounded-lg font-semibold
                           hover:bg">
                Masuk
            </button>

        </form>
        <br>
        <?php if(isset($_GET['error']) && $_GET['error'] == '1') { ?>
    <div id="errorAlert"
         class="text-red-700 text-sm text-center opacity-100 transition-opacity duration-500">
        Username atau Password salah!
    </div>
<?php } ?>
    </div>

    <!-- Kembali -->
    <div class="text-center mt-4">
        <a href="../index.php" class="text-gray-600 hover:text-teal transition">
            ← Kembali ke beranda
        </a>
    </div>

</div>

<!-- JS -->
<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.add("text-teal");
    } else {
        password.type = "password";
        icon.classList.remove("text-teal");
    }
}
</script>
<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.add("text-teal");
    } else {
        password.type = "password";
        icon.classList.remove("text-teal");
    }
}

// Animasi fade out saja (lebih aman)
window.addEventListener("DOMContentLoaded", () => {
    const alertBox = document.getElementById("errorAlert");

    if (alertBox) {
        setTimeout(() => {
            alertBox.classList.add("opacity-0");
        }, 3000);
    }
});
</script>
</body>
</html>
