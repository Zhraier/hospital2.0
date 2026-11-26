<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Tamaraw Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; background-color: #f8f7f3; color: #042414; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background-color: #f8f7f3;
            background-size: 38px 38px;
            background-image: linear-gradient(to right, rgba(4, 36, 20, 0.045) 1px, transparent 1px), linear-gradient(to bottom, rgba(4, 36, 20, 0.045) 1px, transparent 1px);
        }
        .font-serif { font-family: 'Fraunces', Georgia, serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .editorial-card { background: #ffffff; border: 1.5px solid #042414; border-radius: 20px; box-shadow: 0 5px 0 #042414; }
        .sub-editorial-card {
            background-color: #fcfcfa; border: 1px solid #042414; border-radius: 12px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }
        .sub-editorial-card:hover { transform: translateY(-2px); box-shadow: 0 4px 0 #042414; background-color: #ffffff; }
        .btn-tactile {
            position: relative; overflow: hidden; display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; border: 1.5px solid #042414; box-shadow: 0 4px 0 #042414;
            transition: transform 0.12s ease, box-shadow 0.12s ease; cursor: pointer;
        }
        .btn-tactile:hover { transform: translateY(-1.5px); box-shadow: 0 5.5px 0 #042414; }
        .btn-tactile:active { transform: translateY(3px) !important; box-shadow: 0 1px 0 #042414 !important; }
        .btn-feu-green { background-color: #004d25; color: #ffffff; }
        .btn-feu-white { background-color: #ffffff; color: #042414; }
        .tactile-ripple {
            position: absolute; border-radius: 50%; transform: scale(0); animation: ripple-anim 0.5s ease-out;
            background-color: rgba(255, 199, 44, 0.45); pointer-events: none;
        }
        @keyframes ripple-anim { to { transform: scale(4); opacity: 0; } }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<nav class="bg-[#f8f7f3]/95 border-b-[1.5px] border-[#042414] sticky top-0 z-30">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <a href="index.php" class="font-serif font-bold text-xl text-[#042414]">Tamaraw Hospital</a>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="index.php" class="btn-tactile btn-feu-white px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">Home</a>
            <a href="about.php" class="btn-tactile btn-feu-green px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">About Us</a>
            <a href="contact.php" class="btn-tactile btn-feu-white px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">Contact</a>
        </div>
    </div>
</nav>

<main class="flex-grow flex items-center justify-center px-4 sm:px-6 py-10 sm:py-14">
    <div class="w-full max-w-[780px] editorial-card p-8 sm:p-12 bg-white">
        <span class="inline-block bg-[#ffc72c] text-[#042414] border border-[#042414] rounded-md px-3.5 py-1 font-mono text-[11px] sm:text-xs font-semibold uppercase tracking-wider mb-5">
            OUR INSTITUTIONAL MISSION
        </span>

        <h1 class="font-serif text-3xl sm:text-[42px] font-bold text-[#042414] leading-tight mb-5">
            About Tamaraw Hospital
        </h1>

        <p class="text-base sm:text-lg text-stone-600 leading-relaxed mb-10 max-w-2xl">
            Tamaraw Hospital provides 24/7 emergency care, specialized consultations, diagnostic services, and patient-first healthcare management grounded in Fortitude, Excellence, and Uprightness.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="sub-editorial-card p-5 sm:p-6">
                <h3 class="font-serif text-xl font-bold text-[#042414] leading-snug mb-3">24/7 Trauma &amp; ER</h3>
                <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                    Immediate emergency stabilization and ambulance coordination in Sampaloc, Manila.
                </p>
            </div>

            <div class="sub-editorial-card p-5 sm:p-6">
                <h3 class="font-serif text-xl font-bold text-[#042414] leading-snug mb-3">Board-Certified Specialists</h3>
                <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                    Led by Chief Attending Dr. Ganesh alongside cardiology and orthopedic consultants.
                </p>
            </div>

            <div class="sub-editorial-card p-5 sm:p-6">
                <h3 class="font-serif text-xl font-bold text-[#042414] leading-snug mb-3">Digital HMS Registry</h3>
                <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                    Transparent consultancy fees and instant online appointment management.
                </p>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('pointerdown', function (e) {
    const btn = e.target.closest('.btn-tactile');
    if (!btn) return;
    const rect = btn.getBoundingClientRect();
    const ripple = document.createElement('span');
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
    ripple.className = 'tactile-ripple';
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 520);
});
</script>
</body>
</html>