<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("db.php");
/** @var mysqli $con */

if (!isset($_SESSION['dname'])) { 
    header("Location: index.php"); 
    exit(); 
}
$dname = $_SESSION['dname'];

// Fetch doctor details including profile picture
$doc_query = mysqli_query($con, "SELECT * FROM doctreg WHERE username='$dname'");
$doc_data = mysqli_fetch_assoc($doc_query);
$profile_pic = !empty($doc_data['profile_pic']) ? "uploads/doctors/" . $doc_data['profile_pic'] : "uploads/doctors/default_doctor.png";

// Add New Slot Handler
if (isset($_POST['add_slot'])) {
    $slot_date = mysqli_real_escape_string($con, $_POST['slot_date']);
    $start_time = mysqli_real_escape_string($con, $_POST['start_time']);

    // Check if this exact date and time slot already exists for this doctor
    $check_dup = mysqli_query($con, "SELECT * FROM doctor_slots WHERE doctor_name='$dname' AND slot_date='$slot_date' AND start_time='$start_time'");
    
    if (mysqli_num_rows($check_dup) > 0) {
        echo "<script>alert('Error: You already created a time slot for this date and time!'); window.location.href='doctor_dashboard.php';</script>";
        exit();
    }

    // Insert new slot
    $query = "INSERT INTO doctor_slots (doctor_name, slot_date, start_time, is_booked) VALUES ('$dname', '$slot_date', '$start_time', 0)";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Time slot added successfully!'); window.location.href='doctor_dashboard.php';</script>";
        exit();
    } else {
        echo "MySQL Error: " . mysqli_error($con);
        exit();
    }
}

// Delete Unbooked Slot Handler
if (isset($_GET['delete_slot']) && isset($_GET['slot_id'])) {
    $slot_id = intval($_GET['slot_id']);
    mysqli_query($con, "DELETE FROM doctor_slots WHERE slot_id='$slot_id' AND doctor_name='$dname' AND is_booked=0");
    echo "<script>alert('Slot removed successfully'); window.location.href='doctor_dashboard.php';</script>";
    exit();
}

// Cancel Appointment Handler
if (isset($_GET['cancel']) && isset($_GET['ID'])) {
    $id = intval($_GET['ID']);
    mysqli_query($con, "UPDATE appointmenttb SET doctorStatus='0' WHERE ID='$id' AND doctor='$dname'");
    echo "<script>alert('Appointment cancelled'); window.location.href='doctor_dashboard.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard — Tamaraw Hospital</title>
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
            gap: 12px;
        }
        .doc-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid var(--feu-dark);
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
        .nav-actions {
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
        .btn-danger-tactile {
            background: #b91c1c;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 0.82rem;
            border-radius: 8px;
            box-shadow: none;
        }
        .btn-danger-tactile:hover { background: #991b1b; transform: none; box-shadow: none; }

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
            box-shadow: none;
            border: 1.5px solid transparent;
        }
        .tab-btn:hover {
            background: #f2f5f3;
            transform: none;
            box-shadow: none;
        }
        .tab-btn.active {
            background: var(--feu-green);
            color: #ffffff;
            border-color: var(--feu-dark);
            box-shadow: 0 2px 0 var(--feu-dark);
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
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .doc-avatar-lg {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--feu-gold);
        }
        .eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--feu-gold);
            margin-bottom: 4px;
            display: block;
        }
        .card-header-dark h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .card-body {
            padding: 28px 32px 34px;
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
        }
        .toolbar-title h2 {
            font-family: 'Fraunces', serif;
            font-size: 1.35rem;
            color: var(--feu-dark);
        }
        .toolbar-title p {
            font-size: 0.88rem;
            color: var(--muted);
            margin-top: 2px;
        }
        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .input-editorial {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.92rem;
            padding: 10px 14px;
            border: 1.5px solid var(--feu-dark);
            border-radius: 10px;
            background: #ffffff;
            color: var(--feu-dark);
            outline: none;
        }
        .input-editorial:focus {
            border-color: var(--feu-green);
            box-shadow: 0 0 0 3px rgba(0, 77, 37, 0.14);
        }
        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 600px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1.5px solid rgba(4, 36, 20, 0.12);
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
            padding: 14px 16px;
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
        .status-warning { background: #fef3c7; color: #92400e; border: 1px solid rgba(146, 64, 14, 0.3); }
        .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid rgba(153, 27, 27, 0.25); }
        .empty-state {
            text-align: center;
            padding: 36px 20px;
            color: var(--muted);
            font-size: 0.95rem;
        }
        @media (max-width: 900px) {
            .main-wrap { grid-template-columns: 1fr; }
            .topbar { padding: 14px 20px; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="doctor_dashboard.php" class="brand">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" class="doc-avatar-sm" alt="Avatar">
        Tamaraw Hospital
        <span class="brand-badge">Dr. <?php echo htmlspecialchars($dname); ?></span>
    </a>
    <div class="nav-actions">
        <a href="logout.php" class="btn-tactile btn-gold">Logout</a>
    </div>
</header>

<main class="main-wrap">
    <!-- Sidebar Navigation Tabs -->
    <aside class="sidebar-card">
        <span class="sidebar-label">Physician Portal</span>
        <button type="button" class="btn-tactile tab-btn active" data-target="appointments">Patient Appointments</button>
        <button type="button" class="btn-tactile tab-btn" data-target="manage-slots">Manage Time Slots</button>
    </aside>

    <section class="editorial-card">
        <div class="card-header-dark">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" class="doc-avatar-lg" alt="Avatar">
            <div>
                <span class="eyebrow">PHYSICIAN CLINICAL WORKSPACE</span>
                <h1>Welcome, Dr. <?php echo htmlspecialchars($dname); ?></h1>
            </div>
        </div>

        <div class="card-body">
            
            <!-- TAB 1: Patient Appointments -->
            <div class="tab-panel active" id="appointments">
                <div class="toolbar">
                    <div class="toolbar-title">
                        <h2>Scheduled Appointments</h2>
                        <p>Review assigned patient consultations and manage active bookings</p>
                    </div>

                    <form class="search-form" method="POST">
                        <input class="input-editorial" type="search" name="search_contact" placeholder="Enter Contact Number" value="<?php echo isset($_POST['search_contact']) ? htmlspecialchars($_POST['search_contact']) : ''; ?>">
                        <button class="btn-tactile btn-green" type="submit" name="search_submit">Search</button>
                        <?php if (isset($_POST['search_submit']) && !empty($_POST['search_contact'])): ?>
                            <a href="doctor_dashboard.php" class="btn-tactile btn-white">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Appointment Date & Time</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM appointmenttb WHERE doctor='$dname'";
                            if (isset($_POST['search_submit']) && !empty($_POST['search_contact'])) {
                                $contact = mysqli_real_escape_string($con, $_POST['search_contact']);
                                $sql .= " AND contact='$contact'";
                            }
                            $res = mysqli_query($con, $sql);
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr>
                                        <td><strong>#{$row['pid']}</strong></td>
                                        <td>" . htmlspecialchars($row['fname']) . "</td>
                                        <td>" . htmlspecialchars($row['lname']) . "</td>
                                        <td>" . htmlspecialchars($row['gender']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>" . htmlspecialchars($row['contact']) . "</td>
                                        <td>" . htmlspecialchars($row['appdate']) . " <br><small style='color:var(--muted);'>{$row['apptime']}</small></td>
                                        <td>";
                                    if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                        echo "<span class='status-badge status-active'>Active</span>";
                                    } elseif ($row['userStatus'] == 0 && $row['doctorStatus'] == 1) {
                                        echo "<span class='status-badge status-cancelled'>Deleted by Patient</span>";
                                    } elseif ($row['userStatus'] == 1 && $row['doctorStatus'] == 0) {
                                        echo "<span class='status-badge status-cancelled'>Deleted by You</span>";
                                    } else {
                                        echo "<span class='status-badge status-cancelled'>Cancelled</span>";
                                    }
                                    echo "</td><td>";
                                    if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                        echo "<a href='doctor_dashboard.php?ID={$row['ID']}&cancel=update' class='btn-tactile btn-danger-tactile' onclick=\"return confirm('Cancel this appointment?');\">Cancel</a>";
                                    } else {
                                        echo "<span style='color:#6b7c72; font-size:0.85rem;'>Cancelled</span>";
                                    }
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='empty-state'>No appointments found for your schedule.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Manage Time Slots -->
            <div class="tab-panel" id="manage-slots">
                <div class="toolbar-title" style="margin-bottom: 20px;">
                    <h2>Manage Time Slots</h2>
                    <p>Create availability blocks for patient bookings and review slot statuses</p>
                </div>

                <form method="POST" class="form-stack">
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <div class="field-group" style="flex: 1; min-width: 200px;">
                            <label for="slot_date">Slot Date *</label>
                            <input type="date" name="slot_date" id="slot_date" class="input-editorial" style="width: 100%;" required>
                        </div>
                        <div class="field-group" style="flex: 1; min-width: 200px;">
                            <label for="start_time">Slot Time *</label>
                            <input type="time" name="start_time" id="start_time" class="input-editorial" style="width: 100%;" required>
                        </div>
                    </div>
                    <div>
                        <button type="submit" name="add_slot" class="btn-tactile btn-green">Add Available Slot &rarr;</button>
                    </div>
                </form>

                <div class="toolbar-title" style="margin-bottom: 14px;">
                    <h2>Your Created Slots</h2>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Booking Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $slot_res = mysqli_query($con, "SELECT * FROM doctor_slots WHERE doctor_name='$dname' ORDER BY slot_date ASC, start_time ASC");
                            if (mysqli_num_rows($slot_res) > 0) {
                                while ($slot = mysqli_fetch_assoc($slot_res)) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($slot['slot_date']) . "</td>
                                        <td>" . htmlspecialchars($slot['start_time']) . "</td>
                                        <td>";
                                    if ($slot['is_booked'] == 1) {
                                        echo "<span class='status-badge status-warning'>Booked</span>";
                                    } else {
                                        echo "<span class='status-badge status-active'>Available</span>";
                                    }
                                    echo "</td><td>";
                                    if ($slot['is_booked'] == 0) {
                                        echo "<a href='doctor_dashboard.php?slot_id={$slot['slot_id']}&delete_slot=1' class='btn-tactile btn-danger-tactile' onclick='return confirm(\"Delete this unbooked slot?\");'>Delete</a>";
                                    } else {
                                        echo "<span style='color:var(--muted); font-size:0.85rem;'>Locked (Booked)</span>";
                                    }
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='empty-state'>You haven't created any time slots yet.</td></tr>";
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
// Tab Switching Script
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