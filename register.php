<?php
session_start();
include("db.php");
/** @var mysqli $con */

if (isset($_POST['patsub'])) {
    $fname = mysqli_real_escape_string($con, $_POST['fname']);
    $lname = mysqli_real_escape_string($con, $_POST['lname']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $check = mysqli_query($con, "SELECT email FROM patreg WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email already registered!');</script>";
    } else {
        $query = "INSERT INTO patreg(fname, lname, gender, email, contact, password) VALUES ('$fname','$lname','$gender','$email','$contact','$password')";
        if (mysqli_query($con, $query)) {
            $_SESSION['pid'] = mysqli_insert_id($con);
            $_SESSION['username'] = $fname . " " . $lname;
            $_SESSION['fname'] = $fname;
            $_SESSION['lname'] = $lname;
            $_SESSION['gender'] = $gender;
            $_SESSION['contact'] = $contact;
            $_SESSION['email'] = $email;
            echo "<script>alert('Account created successfully!'); window.location.href='patient_dashboard.php';</script>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration — Tamaraw Hospital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --feu-green: #004d25;
            --feu-dark: #042414;
            --feu-gold: #ffc72c;
            --paper: #f8f7f3;
            --grid-line: rgba(4, 36, 20, 0.06);
            --muted: #4a5d52;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--paper);
            background-image:
                linear-gradient(to right, var(--grid-line) 1px, transparent 1px),
                linear-gradient(to bottom, var(--grid-line) 1px, transparent 1px);
            background-size: 40px 40px;
            color: var(--feu-dark);
            min-height: 100vh;
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1.5px solid rgba(4, 36, 20, 0.14);
            padding: 16px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }
        .brand {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            font-size: 1.35rem;
            color: var(--feu-dark);
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-tactile {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 10px;
            border: 1.5px solid var(--feu-dark);
            padding: 9px 18px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 3px 0 var(--feu-dark);
            transition: transform 0.1s ease, box-shadow 0.1s ease, background-color 0.15s ease;
        }
        .btn-tactile:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 0 var(--feu-dark);
        }
        .btn-tactile:active {
            transform: translateY(2px);
            box-shadow: 0 1px 0 var(--feu-dark);
        }
        .btn-green { background: var(--feu-green); color: #ffffff; }
        .btn-green:hover { background: #003b1c; }
        .btn-gold { background: var(--feu-gold); color: var(--feu-dark); }
        .btn-gold:hover { background: #f2ba1d; }
        .btn-white { background: #ffffff; color: var(--feu-dark); }

        .center-wrap {
            max-width: 680px;
            margin: 54px auto;
            padding: 0 20px;
        }
        .editorial-card {
            background: #ffffff;
            border: 2px solid var(--feu-dark);
            border-radius: 18px;
            box-shadow: 0 6px 0 var(--feu-dark);
            overflow: hidden;
        }
        .card-header-dark {
            background: var(--feu-dark);
            color: #ffffff;
            padding: 26px 32px;
        }
        .eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--feu-gold);
            margin-bottom: 6px;
            display: block;
        }
        .card-header-dark h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.7rem;
            font-weight: 700;
        }
        .card-body {
            padding: 30px 32px 32px;
        }
        .sub-head {
            margin-bottom: 22px;
        }
        .sub-head h2 {
            font-family: 'Fraunces', serif;
            font-size: 1.3rem;
            color: var(--feu-dark);
        }
        .sub-head p {
            font-size: 0.88rem;
            color: var(--muted);
            margin-top: 3px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .field-group {
            margin-bottom: 16px;
        }
        .field-group label {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.74rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
            color: var(--feu-dark);
        }
        .input-editorial {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.94rem;
            padding: 12px 14px;
            border: 1.5px solid var(--feu-dark);
            border-radius: 10px;
            background: #ffffff;
            color: var(--feu-dark);
            width: 100%;
            outline: none;
        }
        .input-editorial:focus {
            border-color: var(--feu-green);
            box-shadow: 0 0 0 3px rgba(0, 77, 37, 0.14);
        }
        .gender-row {
            display: flex;
            gap: 14px;
        }
        .gender-option {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border: 1.5px solid var(--feu-dark);
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.92rem;
            background: #ffffff;
            transition: background-color 0.15s ease;
        }
        .gender-option:hover {
            background: #f2f5f3;
        }
        .gender-option input[type="radio"] {
            accent-color: var(--feu-green);
            width: 16px;
            height: 16px;
        }
        .btn-submit-full {
            width: 100%;
            padding: 13px 20px;
            font-size: 0.98rem;
            margin-top: 6px;
        }
        .card-footer-bar {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(4, 36, 20, 0.14);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.9rem;
            color: var(--muted);
        }
        @media (max-width: 600px) {
            .grid-2 { grid-template-columns: 1fr; gap: 0; }
            .topbar { padding: 14px 20px; }
            .card-header-dark, .card-body { padding: 22px; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand">Tamaraw Hospital</a>
    <nav class="nav-links">
        <a href="index.php" class="btn-tactile btn-white">Home</a>
        <a href="about.php" class="btn-tactile btn-white">About Us</a>
        <a href="contact.php" class="btn-tactile btn-white">Contact</a>
    </nav>
</header>

<main class="center-wrap">
    <section class="editorial-card">
        <div class="card-header-dark">
            <span class="eyebrow">NEW PATIENT ENROLLMENT</span>
            <h1>Patient Registration</h1>
        </div>

        <div class="card-body">
            <div class="sub-head">
                <h2>Create Patient Account</h2>
                <p>Register once to book consultations and view your clinical records</p>
            </div>

            <form method="POST">
                <div class="grid-2">
                    <div class="field-group">
                        <label>First Name *</label>
                        <input type="text" name="fname" class="input-editorial" placeholder="Juan" required>
                    </div>
                    <div class="field-group">
                        <label>Last Name *</label>
                        <input type="text" name="lname" class="input-editorial" placeholder="Dela Cruz" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="field-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" class="input-editorial" placeholder="juan.delacruz@feu.edu.ph" required>
                    </div>
                    <div class="field-group">
                        <label>Contact Number *</label>
                        <input type="text" name="contact" class="input-editorial" placeholder="09171234567" required>
                    </div>
                </div>

                <div class="field-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="input-editorial" placeholder="••••••••" required>
                </div>

                <div class="field-group">
                    <label>Gender *</label>
                    <div class="gender-row">
                        <label class="gender-option">
                            <input type="radio" name="gender" value="Male" checked> Male
                        </label>
                        <label class="gender-option">
                            <input type="radio" name="gender" value="Female"> Female
                        </label>
                    </div>
                </div>

                <button type="submit" name="patsub" class="btn-tactile btn-green btn-submit-full">Register Patient Account &rarr;</button>
            </form>

            <div class="card-footer-bar">
                <span>Already have a patient account?</span>
                <a href="index.php" class="btn-tactile btn-gold">Back to Login</a>
            </div>
        </div>
    </section>
</main>

</body>
</html>