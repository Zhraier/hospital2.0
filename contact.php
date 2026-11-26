<?php
include("db.php");
$statusMessage = "";
if (isset($_POST['btnSubmit'])) {
    $name = mysqli_real_escape_string($con, $_POST['txtName']);
    $email = mysqli_real_escape_string($con, $_POST['txtEmail']);
    $contact = mysqli_real_escape_string($con, $_POST['txtPhone']);
    $message = mysqli_real_escape_string($con, $_POST['txtMsg']);
    $query = "INSERT INTO contact(name, email, contact, message) VALUES ('$name','$email','$contact','$message')";
    if (mysqli_query($con, $query)) {
        $statusMessage = "Message sent successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Tamaraw Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; background-color: #f8f7f3; color: #042414; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background-color: #f8f7f3;
            background-size: 38px 38px;
            background-image: linear-gradient(to right, rgba(4, 36, 20, 0.045) 1px, transparent 1px), linear-gradient(to bottom, rgba(4, 36, 20, 0.045) 1px, transparent 1px);
        }
        .font-serif { font-family: 'Fraunces', Georgia, serif; }
        .editorial-card { background: #ffffff; border: 1.5px solid #042414; border-radius: 20px; box-shadow: 0 5px 0 #042414; }
        .editorial-input { width: 100%; background: #ffffff; border: 1px solid #042414; border-radius: 10px; padding: 13px 16px; color: #042414; }
        .editorial-input:focus { outline: none; box-shadow: 0 3.5px 0 #ffc72c; }
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
            <a href="about.php" class="btn-tactile btn-feu-white px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">About Us</a>
            <a href="contact.php" class="btn-tactile btn-feu-green px-3.5 py-1.5 rounded-lg text-xs sm:text-sm">Contact</a>
        </div>
    </div>
</nav>

<main class="flex-grow flex items-center justify-center px-4 sm:px-6 py-10 sm:py-14">
    <div class="w-full max-w-[780px] editorial-card p-8 sm:p-11 bg-white">
        <div class="w-full min-h-[38px] bg-[#d2e3d9] border border-[#042414] rounded-[10px] px-4 py-2 mb-6 flex items-center text-xs sm:text-sm font-semibold text-[#042414]">
            <?php echo htmlspecialchars($statusMessage); ?>
        </div>

        <h1 class="font-serif text-2xl sm:text-[32px] font-bold text-[#042414] mb-6">
            Drop Us a Message
        </h1>

        <form method="POST" class="space-y-4">
            <div>
                <input type="text" name="txtName" class="editorial-input" placeholder="Your Name *" required>
            </div>
            <div>
                <input type="email" name="txtEmail" class="editorial-input" placeholder="Your Email *" required>
            </div>
            <div>
                <input type="text" name="txtPhone" class="editorial-input" placeholder="Your Phone Number *" required>
            </div>
            <div>
                <textarea name="txtMsg" rows="4" class="editorial-input" placeholder="Your Message *" required></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" name="btnSubmit" class="btn-tactile btn-feu-green px-6 py-3 rounded-[10px] text-sm sm:text-base">
                    Send Message
                </button>
            </div>
        </form>
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