<?php
session_start();
include("db.php");

$loginError = "";

// Patient Login
if (isset($_POST['patlogin'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $res = mysqli_query($con, "SELECT * FROM patreg WHERE email='$email' AND password='$password'");
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['pid'] = $row['pid'];
        $_SESSION['username'] = $row['fname'] . " " . $row['lname'];
        $_SESSION['fname'] = $row['fname'];
        $_SESSION['lname'] = $row['lname'];
        $_SESSION['gender'] = $row['gender'];
        $_SESSION['contact'] = $row['contact'];
        $_SESSION['email'] = $row['email'];
        header("Location: patient_dashboard.php");
        exit();
    } else {
        $loginError = "Invalid Patient Credentials";
    }
}

// Doctor Login
if (isset($_POST['doclogin'])) {
    $dname = mysqli_real_escape_string($con, $_POST['dname']);
    $dpass = mysqli_real_escape_string($con, $_POST['dpass']);
    $res = mysqli_query($con, "SELECT * FROM doctreg WHERE username='$dname' AND password='$dpass'");
    if (mysqli_num_rows($res) == 1) {
        $_SESSION['dname'] = $dname;
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        $loginError = "Invalid Doctor Credentials";
    }
}

// Admin Login
if (isset($_POST['admlogin'])) {
    $aname = mysqli_real_escape_string($con, $_POST['aname']);
    $apass = mysqli_real_escape_string($con, $_POST['apass']);
    $res = mysqli_query($con, "SELECT * FROM admin WHERE username='$aname' AND password='$apass'");
    if (mysqli_num_rows($res) == 1) {
        $_SESSION['admin'] = $aname;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $loginError = "Invalid Admin Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamaraw Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        .btn-tactile {
            position: relative; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            font-weight: 700; border: 1.5px solid #042414; box-shadow: 0 4px 0 #042414;
            transition: transform 0.12s ease, box-shadow 0.12s ease, background-color 0.15s ease; cursor: pointer;
        }
        .btn-tactile:hover { transform: translateY(-1.5px); box-shadow: 0 5.5px 0 #042414; }
        .btn-tactile:active { transform: translateY(3px) !important; box-shadow: 0 1px 0 #042414 !important; }
        .btn-feu-green { background-color: #004d25; color: #ffffff; }
        .btn-feu-gold { background-color: #ffc72c; color: #042414; }
        .btn-feu-white { background-color: #ffffff; color: #042414; }
        .role-pill-active { background-color: #004d25; color: #ffffff; border: 1.5px solid #042414; box-shadow: 0 3px 0 #042414; }
        .role-pill-inactive { background-color: #ffffff; color: #042414; border: 1px solid #042414; box-shadow: none; }
        .editorial-input { width: 100%; background: #ffffff; border: 1px solid #042414; border-radius: 10px; padding: 13px 16px; color: #042414; }
        .editorial-input:focus { outline: none; box-shadow: 0 3.5px 0 #ffc72c; }
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
            <a href="index.php" class="btn-tactile btn-feu-green px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">Home</a>
            <a href="about.php" class="btn-tactile btn-feu-white px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">About Us</a>
            <a href="contact.php" class="btn-tactile btn-feu-white px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">Contact</a>
        </div>
    </div>
</nav>

<main class="flex-grow flex items-center justify-center px-4 sm:px-6 py-10 sm:py-14">
    <div class="w-full max-w-[820px] editorial-card overflow-hidden">
        <div class="bg-[#042414] px-6 sm:px-9 py-7 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="font-mono text-[11px] sm:text-xs font-semibold tracking-widest uppercase text-[#ffc72c] mb-1.5">UNIFIED ACCESS PORTAL</p>
                <h1 class="font-serif text-2xl sm:text-[28px] font-bold text-white leading-tight">Hospital Management Login</h1>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-md border border-[#ffc72c]/80 bg-[#004d25]/35 text-[#ffc72c] font-mono text-xs font-semibold">
                <i class="fa-solid fa-lock text-[11px]"></i>
                <span>SSL / MySQL</span>
            </div>
        </div>

        <div class="p-6 sm:p-9 bg-white">
            <?php if (!empty($loginError)): ?>
                <div class="mb-5 p-3.5 rounded-[10px] bg-[#d2e3d9] border border-[#042414] text-sm font-semibold text-[#042414]">
                    <?php echo htmlspecialchars($loginError); ?>
                </div>
            <?php endif; ?>

            <div class="border border-[#042414] rounded-[14px] p-1.5 grid grid-cols-3 gap-2 mb-8 bg-white">
                <button type="button" onclick="switchRole('patient')" id="role-btn-patient" class="btn-tactile role-pill-active py-2.5 px-3 rounded-[10px] text-xs sm:text-sm">
                    <i class="fa-solid fa-hospital-user"></i><span>Patient</span>
                </button>
                <button type="button" onclick="switchRole('doctor')" id="role-btn-doctor" class="btn-tactile role-pill-inactive py-2.5 px-3 rounded-[10px] text-xs sm:text-sm">
                    <i class="fa-solid fa-user-doctor"></i><span>Doctor</span>
                </button>
                <button type="button" onclick="switchRole('admin')" id="role-btn-admin" class="btn-tactile role-pill-inactive py-2.5 px-3 rounded-[10px] text-xs sm:text-sm">
                    <i class="fa-solid fa-user-shield"></i><span>Admin</span>
                </button>
            </div>

            <!-- Patient Login -->
            <div id="role-panel-patient">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#042414]">Patient Portal Login</h2>
                        <p class="text-xs sm:text-sm text-stone-600 mt-1">Book appointments &amp; view your clinical history</p>
                    </div>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded bg-[#d7efe2] text-[#004d25]">patreg</span>
                </div>
                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block font-mono text-[11px] font-semibold uppercase tracking-wider text-[#042414] mb-2">PATIENT EMAIL ID *</label>
                        <input type="email" name="email" class="editorial-input" placeholder="juan.delacruz@feu.edu.ph" required>
                    </div>
                    <div>
                        <label class="block font-mono text-[11px] font-semibold uppercase tracking-wider text-[#042414] mb-2">PASSWORD *</label>
                        <input type="password" name="password" class="editorial-input" placeholder="•••••••••" required>
                    </div>
                    <button type="submit" name="patlogin" class="btn-tactile btn-feu-green w-full py-3.5 rounded-[10px] text-sm sm:text-base mt-1">
                        <span>Login as Patient</span><i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
                <div class="mt-7 pt-5 border-t border-stone-200 flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs sm:text-sm text-stone-600">New to Tamaraw Hospital?</span>
                    <a href="register.php" class="btn-tactile btn-feu-gold px-4 py-2 rounded-[10px] text-xs font-bold">Create Account (register.php)</a>
                </div>
            </div>

            <!-- Doctor Login -->
            <div id="role-panel-doctor" class="hidden">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#042414]">Doctor Portal Login</h2>
                        <p class="text-xs sm:text-sm text-stone-600 mt-1">Manage daily consultations &amp; patient appointments</p>
                    </div>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded bg-[#d7efe2] text-[#004d25]">doctreg</span>
                </div>
                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block font-mono text-[11px] font-semibold uppercase tracking-wider text-[#042414] mb-2">DOCTOR USERNAME *</label>
                        <input type="text" name="dname" class="editorial-input" placeholder="Ganesh" required>
                    </div>
                    <div>
                        <label class="block font-mono text-[11px] font-semibold uppercase tracking-wider text-[#042414] mb-2">PASSWORD *</label>
                        <input type="password" name="dpass" class="editorial-input" placeholder="•••••••••" required>
                    </div>
                    <button type="submit" name="doclogin" class="btn-tactile btn-feu-green w-full py-3.5 rounded-[10px] text-sm sm:text-base mt-1">
                        <span>Login as Doctor</span><i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            <!-- Admin Login -->
            <div id="role-panel-admin" class="hidden">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-[#042414]">Admin Portal Login</h2>
                        <p class="text-xs sm:text-sm text-stone-600 mt-1">Manage hospital doctors, patient records &amp; inquiries</p>
                    </div>
                    <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded bg-[#d7efe2] text-[#004d25]">admin</span>
                </div>
                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block font-mono text-[11px] font-semibold uppercase tracking-wider text-[#042414] mb-2">ADMIN USERNAME *</label>
                        <input type="text" name="aname" class="editorial-input" placeholder="admin" required>
                    </div>
                    <div>
                        <label class="block font-mono text-[11px] font-semibold uppercase tracking-wider text-[#042414] mb-2">PASSWORD *</label>
                        <input type="password" name="apass" class="editorial-input" placeholder="•••••••••" required>
                    </div>
                    <button type="submit" name="admlogin" class="btn-tactile btn-feu-green w-full py-3.5 rounded-[10px] text-sm sm:text-base mt-1">
                        <span>Login as Admin</span><i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
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

function switchRole(role) {
    ['patient', 'doctor', 'admin'].forEach(r => {
        const panel = document.getElementById(`role-panel-${r}`);
        const btn = document.getElementById(`role-btn-${r}`);
        if (r === role) {
            panel.classList.remove('hidden');
            btn.classList.remove('role-pill-inactive');
            btn.classList.add('role-pill-active');
        } else {
            panel.classList.add('hidden');
            btn.classList.remove('role-pill-active');
            btn.classList.add('role-pill-inactive');
        }
    });
}
</script>
</body>
</html>