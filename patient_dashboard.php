<?php
session_start();
include("db.php");
/** @var mysqli $con */

if (!isset($_SESSION['pid'])) {
    header("Location: index.php");
    exit();
}

$pid = $_SESSION['pid'];
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$gender = $_SESSION['gender'];
$email = $_SESSION['email'];
$contact = $_SESSION['contact'];

// Book Appointment (Using slot_id from doctor's created schedule)
if (isset($_POST['app-submit'])) {
    $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
    $docFees = mysqli_real_escape_string($con, $_POST['docFees']);
    $slot_id = intval($_POST['slot_id']); 

    // Fetch the specific slot date and time from the database using slot_id
    $slot_query = mysqli_query($con, "SELECT slot_date, start_time FROM doctor_slots WHERE slot_id = '$slot_id' AND is_booked = 0");
    $slot_data = mysqli_fetch_assoc($slot_query);

    if ($slot_data) {
        $appdate = $slot_data['slot_date'];
        $apptime = $slot_data['start_time'];

        // Insert into appointmenttb
        $query = "INSERT INTO appointmenttb(pid, fname, lname, gender, email, contact, doctor, docFees, appdate, apptime, userStatus, doctorStatus) 
                  VALUES ('$pid','$fname','$lname','$gender','$email','$contact','$doctor','$docFees','$appdate','$apptime','1','1')";
        
        if (mysqli_query($con, $query)) {
            // Mark the slot as booked (is_booked = 1) so no one else can select it
            mysqli_query($con, "UPDATE doctor_slots SET is_booked = 1 WHERE slot_id = '$slot_id'");
            
            echo "<script>alert('Your appointment successfully booked'); window.location.href='patient_dashboard.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Selected slot is invalid or has already been booked.');</script>";
    }
}

// Cancel Appointment
if (isset($_GET['cancel']) && isset($_GET['ID'])) {
    $id = intval($_GET['ID']);
    mysqli_query($con, "UPDATE appointmenttb SET userStatus='0' WHERE ID='$id' AND pid='$pid'");
    echo "<script>alert('Your appointment successfully cancelled'); window.location.href='patient_dashboard.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard — Tamaraw Hospital</title>
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
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            background: #e6f2ec;
            color: var(--feu-green);
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid rgba(0, 77, 37, 0.25);
        }
        .btn-tactile {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 10px;
            border: 1.5px solid var(--feu-dark);
            padding: 10px 18px;
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
        .btn-danger-tactile {
            background: #b91c1c;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 0.82rem;
            border-radius: 8px;
        }
        .btn-danger-tactile:hover { background: #991b1b; }

        .main-wrap {
            max-width: 1180px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 26px;
            align-items: start;
        }
        .sidebar-card {
            background: #ffffff;
            border: 2px solid var(--feu-dark);
            border-radius: 16px;
            box-shadow: 0 5px 0 var(--feu-dark);
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sidebar-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            padding: 4px 8px 2px;
        }
        .tab-btn {
            width: 100%;
            text-align: left;
            justify-content: flex-start;
            background: #ffffff;
            color: var(--feu-dark);
            padding: 11px 14px;
            font-size: 0.9rem;
        }
        .tab-btn.active {
            background: var(--feu-green);
            color: #ffffff;
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
            padding: 24px 30px;
        }
        .eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--feu-gold);
            margin-bottom: 5px;
            display: block;
        }
        .card-header-dark h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.65rem;
            font-weight: 700;
        }
        .card-body {
            padding: 28px 30px 32px;
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        .panel-head {
            margin-bottom: 22px;
        }
        .panel-head h2 {
            font-family: 'Fraunces', serif;
            font-size: 1.35rem;
            color: var(--feu-dark);
        }
        .panel-head p {
            font-size: 0.88rem;
            color: var(--muted);
            margin-top: 3px;
        }
        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 540px;
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
            padding: 11px 14px;
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
        .input-editorial[readonly] {
            background: #f2f5f3;
            color: var(--muted);
            cursor: not-allowed;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border: 1.5px solid var(--feu-dark);
            border-radius: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }
        thead {
            background: #f2f5f3;
            border-bottom: 1.5px solid var(--feu-dark);
        }
        th {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--feu-dark);
            padding: 14px 16px;
            white-space: nowrap;
        }
        td {
            padding: 13px 16px;
            border-bottom: 1px solid rgba(4, 36, 20, 0.1);
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background-color: #fafbf9; }
        .status-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.74rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            display: inline-block;
            white-space: nowrap;
        }
        .status-active { background: #e6f2ec; color: var(--feu-green); border: 1px solid rgba(0, 77, 37, 0.3); }
        .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid rgba(153, 27, 27, 0.25); }
        .empty-state {
            text-align: center;
            padding: 32px 16px;
            color: var(--muted);
        }
        @media (max-width: 900px) {
            .main-wrap { grid-template-columns: 1fr; }
            .topbar { padding: 14px 20px; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="patient_dashboard.php" class="brand">
        Tamaraw Hospital
        <span class="brand-badge">PID #<?php echo htmlspecialchars($pid); ?></span>
    </a>
    <a href="logout.php" class="btn-tactile btn-gold">Logout</a>
</header>

<main class="main-wrap">
    <aside class="sidebar-card">
        <span class="sidebar-label">Patient Services</span>
        <button type="button" class="btn-tactile tab-btn active" data-target="book">Book Appointment</button>
        <button type="button" class="btn-tactile tab-btn" data-target="history">Appointment History</button>
    </aside>

    <section class="editorial-card">
        <div class="card-header-dark">
            <span class="eyebrow">PATIENT CLINICAL PORTAL</span>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? $fname); ?></h1>
        </div>

        <div class="card-body">
            <!-- 1. Book Appointment (With Slot Dropdown) -->
            <div class="tab-panel active" id="book">
                <div class="panel-head">
                    <h2>Create an Appointment</h2>
                    <p>Select an attending physician and choose from their available schedule slots</p>
                </div>

                <form method="POST" class="form-stack">
                    <div class="field-group">
                        <label for="doctor">Doctors *</label>
                        <select name="doctor" id="doctor" class="input-editorial" onchange="updateFees()" required>
                            <option value="" disabled selected>Select Doctor</option>
                            <?php
                            $docs = mysqli_query($con, "SELECT * FROM doctreg");
                            while ($row = mysqli_fetch_assoc($docs)) {
                                echo "<option value='" . htmlspecialchars($row['username']) . "' data-fees='" . htmlspecialchars($row['docFees']) . "'>Dr. " . htmlspecialchars($row['username']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="docFees">Consultancy Fees (PHP)</label>
                        <input type="text" name="docFees" id="docFees" class="input-editorial" placeholder="Auto-filled upon selecting doctor" readonly required>
                    </div>

                    <div class="field-group">
                        <label for="slot_id">Available Appointment Slots *</label>
                        <select name="slot_id" id="slot_id" class="input-editorial" required>
                            <option value="" disabled selected>Select Available Date & Time Slot</option>
                            <?php
                            // Fetch only unbooked slots created by doctors
                            $slots_res = mysqli_query($con, "SELECT * FROM doctor_slots WHERE is_booked = 0 ORDER BY slot_date ASC, start_time ASC");
                            while ($slot = mysqli_fetch_assoc($slots_res)) {
                                echo "<option value='{$slot['slot_id']}'>Dr. {$slot['doctor_name']} — Date: {$slot['slot_date']} | Time: {$slot['start_time']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <button type="submit" name="app-submit" class="btn-tactile btn-green">Create New Entry &rarr;</button>
                    </div>
                </form>
            </div>

            <!-- 2. Appointment History -->
            <div class="tab-panel" id="history">
                <div class="panel-head">
                    <h2>Appointment History</h2>
                    <p>View your booked consultations and cancel active schedules if needed</p>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Consultancy Fees</th>
                                <th>Appointment Date</th>
                                <th>Appointment Time</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($con, "SELECT * FROM appointmenttb WHERE pid='$pid'");
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr>
                                        <td><strong>Dr. " . htmlspecialchars($row['doctor']) . "</strong></td>
                                        <td>₱" . htmlspecialchars($row['docFees']) . "</td>
                                        <td>" . htmlspecialchars($row['appdate']) . "</td>
                                        <td>" . htmlspecialchars($row['apptime']) . "</td>
                                        <td>";
                                    if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                        echo "<span class='status-badge status-active'>Active</span>";
                                    } elseif ($row['userStatus'] == 0 && $row['doctorStatus'] == 1) {
                                        echo "<span class='status-badge status-cancelled'>Deleted by You</span>";
                                    } elseif ($row['userStatus'] == 1 && $row['doctorStatus'] == 0) {
                                        echo "<span class='status-badge status-cancelled'>Deleted by Doctor</span>";
                                    } else {
                                        echo "<span class='status-badge status-cancelled'>Cancelled</span>";
                                    }
                                    echo "</td><td>";
                                    if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                        echo "<a href='patient_dashboard.php?ID={$row['ID']}&cancel=update' class='btn-tactile btn-danger-tactile' onclick=\"return confirm('Cancel this appointment?');\">Cancel</a>";
                                    } else {
                                        echo "<span style='color:#6b7c72; font-size:0.85rem;'>Cancelled</span>";
                                    }
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='empty-state'>You have no appointments on record yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function updateFees() {
    var select = document.getElementById("doctor");
    var fees = select.options[select.selectedIndex].getAttribute("data-fees");
    document.getElementById("docFees").value = fees;
}

const tabButtons = document.querySelectorAll('.tab-btn');
const tabPanels = document.querySelectorAll('.tab-panel');

tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        tabPanels.forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const target = document.getElementById(btn.dataset.target);
        if (target) target.classList.add('active');
    });
});
</script>

</body>
</html>