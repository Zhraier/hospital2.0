<?php
session_start();
include("db.php");
/** @var mysqli $con */

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Ensure uploads directory exists
$upload_dir = "uploads/doctors/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Add Doctor
if (isset($_POST['docsub'])) {
    $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
    $dpassword = mysqli_real_escape_string($con, $_POST['dpassword']);
    $demail = mysqli_real_escape_string($con, $_POST['demail']);
    $docFees = mysqli_real_escape_string($con, $_POST['docFees']);
    
    $profile_pic = "default_doctor.png";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = "doc_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $profile_pic = $new_file_name;
            }
        }
    }

    $query = "INSERT INTO doctreg(username, password, email, docFees, profile_pic) VALUES ('$doctor','$dpassword','$demail','$docFees','$profile_pic')";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Doctor added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding doctor (Email may already exist).');</script>";
    }
}

// Edit Doctor
if (isset($_POST['docedit'])) {
    $edit_id = intval($_POST['edit_id']);
    $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
    $dpassword = mysqli_real_escape_string($con, $_POST['dpassword']);
    $demail = mysqli_real_escape_string($con, $_POST['demail']);
    $docFees = mysqli_real_escape_string($con, $_POST['docFees']);

    // Check if new image uploaded
    $img_update_sql = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = "doc_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $img_update_sql = ", profile_pic='$new_file_name'";
            }
        }
    }

    $query = "UPDATE doctreg SET username='$doctor', password='$dpassword', email='$demail', docFees='$docFees' $img_update_sql WHERE id='$edit_id'";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Doctor updated successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating doctor.');</script>";
    }
}

// Delete Doctor
if (isset($_POST['docdel'])) {
    $demail = mysqli_real_escape_string($con, $_POST['demail']);
    mysqli_query($con, "DELETE FROM doctreg WHERE email='$demail'");
    if (mysqli_affected_rows($con) > 0) {
        echo "<script>alert('Doctor removed successfully!');</script>";
    } else {
        echo "<script>alert('Doctor email not found.');</script>";
    }
}

// Preserve active tab across POST searches
$activeTab = 'doctors';
if (isset($_POST['search_pat'])) $activeTab = 'patients';
elseif (isset($_POST['docsub'])) $activeTab = 'add-doc';
elseif (isset($_POST['docedit'])) $activeTab = 'doctors';
elseif (isset($_POST['docdel'])) $activeTab = 'del-doc';
elseif (isset($_GET['edit_id'])) $activeTab = 'edit-doc';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Tamaraw Hospital</title>
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
        .btn-white { background: #ffffff; color: var(--feu-dark); }
        .btn-danger-tactile { background: #b91c1c; color: #ffffff; padding: 6px 12px; font-size: 0.82rem; border-radius: 8px; box-shadow: none; }
        .btn-danger-tactile:hover { background: #991b1b; }
        .btn-edit-tactile { background: #2563eb; color: #ffffff; padding: 6px 12px; font-size: 0.82rem; border-radius: 8px; box-shadow: none; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-edit-tactile:hover { background: #1d4ed8; color: #ffffff; }

        .main-wrap {
            max-width: 1220px;
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
        .tab-btn:hover { background: #f2f5f3; }
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
            margin-bottom: 20px;
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
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .input-editorial {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.92rem;
            padding: 11px 14px;
            border: 1.5px solid var(--feu-dark);
            border-radius: 10px;
            background: #ffffff;
            color: var(--feu-dark);
            flex: 1;
            min-width: 220px;
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
            max-width: 520px;
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
        .field-group .input-editorial {
            width: 100%;
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
        .doc-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid var(--feu-dark);
        }
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
    <a href="admin_dashboard.php" class="brand">
        Tamaraw Hospital
        <span class="brand-badge">Admin Portal</span>
    </a>
    <a href="logout.php" class="btn-tactile btn-gold">Logout</a>
</header>

<main class="main-wrap">
    <aside class="sidebar-card">
        <span class="sidebar-label">Hospital Controls</span>
        <button type="button" class="btn-tactile tab-btn <?php echo $activeTab === 'doctors' ? 'active' : ''; ?>" data-target="doctors">Doctor List</button>
        <button type="button" class="btn-tactile tab-btn <?php echo $activeTab === 'patients' ? 'active' : ''; ?>" data-target="patients">Patient List</button>
        <button type="button" class="btn-tactile tab-btn <?php echo $activeTab === 'appointments' ? 'active' : ''; ?>" data-target="appointments">Appointment Details</button>
        <button type="button" class="btn-tactile tab-btn <?php echo $activeTab === 'add-doc' ? 'active' : ''; ?>" data-target="add-doc">Add Doctor</button>
        <button type="button" class="btn-tactile tab-btn <?php echo $activeTab === 'del-doc' ? 'active' : ''; ?>" data-target="del-doc">Delete Doctor</button>
        <button type="button" class="btn-tactile tab-btn <?php echo $activeTab === 'queries' ? 'active' : ''; ?>" data-target="queries">Queries</button>
    </aside>

    <section class="editorial-card">
        <div class="card-header-dark">
            <span class="eyebrow">CENTRAL ADMINISTRATION</span>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?></h1>
        </div>

        <div class="card-body">
            <!-- 1. Doctor List -->
            <div class="tab-panel <?php echo $activeTab === 'doctors' ? 'active' : ''; ?>" id="doctors">
                <div class="panel-head">
                    <h2>Doctor Registry</h2>
                    <p>Search and inspect registered attending physicians, profile pictures, and consultation rates</p>
                </div>
                <form method="POST" class="search-bar">
                    <input type="text" name="doc_email" class="input-editorial" placeholder="Enter Email ID" value="<?php echo isset($_POST['doc_email']) ? htmlspecialchars($_POST['doc_email']) : ''; ?>">
                    <button type="submit" name="search_doc" class="btn-tactile btn-green">Search</button>
                    <?php if (isset($_POST['search_doc']) && !empty($_POST['doc_email'])): ?>
                        <a href="admin_dashboard.php" class="btn-tactile btn-white">Reset</a>
                    <?php endif; ?>
                </form>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Doctor Name</th>
                                <th>Password</th>
                                <th>Email</th>
                                <th>Fees</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM doctreg";
                            if (isset($_POST['search_doc']) && !empty($_POST['doc_email'])) {
                                $doc_email = mysqli_real_escape_string($con, $_POST['doc_email']);
                                $sql .= " WHERE email='$doc_email'";
                            }
                            $res = mysqli_query($con, $sql);
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $pic = !empty($row['profile_pic']) ? "uploads/doctors/" . $row['profile_pic'] : "uploads/doctors/default_doctor.png";
                                    echo "<tr>
                                        <td><img src='" . htmlspecialchars($pic) . "' class='doc-avatar' alt='Avatar'></td>
                                        <td><strong>" . htmlspecialchars($row['username']) . "</strong></td>
                                        <td><code>" . htmlspecialchars($row['password']) . "</code></td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>₱" . htmlspecialchars($row['docFees']) . "</td>
                                        <td><a href='admin_dashboard.php?edit_id=" . $row['id'] . "' class='btn-edit-tactile'>Edit</a></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='empty-state'>No doctor records matched your query.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Patient List -->
            <div class="tab-panel <?php echo $activeTab === 'patients' ? 'active' : ''; ?>" id="patients">
                <div class="panel-head">
                    <h2>Registered Patients</h2>
                    <p>Filter patient accounts by registered contact number</p>
                </div>
                <form method="POST" class="search-bar">
                    <input type="text" name="pat_contact" class="input-editorial" placeholder="Enter Contact Number" value="<?php echo isset($_POST['pat_contact']) ? htmlspecialchars($_POST['pat_contact']) : ''; ?>">
                    <button type="submit" name="search_pat" class="btn-tactile btn-green">Search</button>
                    <?php if (isset($_POST['search_pat']) && !empty($_POST['pat_contact'])): ?>
                        <a href="admin_dashboard.php" class="btn-tactile btn-white">Reset</a>
                    <?php endif; ?>
                </form>
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
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM patreg";
                            if (isset($_POST['search_pat']) && !empty($_POST['pat_contact'])) {
                                $pat_contact = mysqli_real_escape_string($con, $_POST['pat_contact']);
                                $sql .= " WHERE contact='$pat_contact'";
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
                                        <td><code>" . htmlspecialchars($row['password']) . "</code></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='empty-state'>No patient records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Appointment List -->
            <div class="tab-panel <?php echo $activeTab === 'appointments' ? 'active' : ''; ?>" id="appointments">
                <div class="panel-head">
                    <h2>Appointment Details</h2>
                    <p>Hospital-wide consultation log and live booking statuses</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Doctor</th>
                                <th>Fees</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($con, "SELECT * FROM appointmenttb");
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                        $badge = "<span class='status-badge status-active'>Active</span>";
                                    } elseif ($row['userStatus'] == 0 && $row['doctorStatus'] == 1) {
                                        $badge = "<span class='status-badge status-cancelled'>Deleted by Patient</span>";
                                    } else {
                                        $badge = "<span class='status-badge status-cancelled'>Deleted by Doctor</span>";
                                    }
                                    echo "<tr>
                                        <td><strong>" . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . "</strong></td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>" . htmlspecialchars($row['contact']) . "</td>
                                        <td>Dr. " . htmlspecialchars($row['doctor']) . "</td>
                                        <td>₱" . htmlspecialchars($row['docFees']) . "</td>
                                        <td>" . htmlspecialchars($row['appdate']) . "</td>
                                        <td>" . htmlspecialchars($row['apptime']) . "</td>
                                        <td>$badge</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='empty-state'>No appointments booked yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Add Doctor -->
            <div class="tab-panel <?php echo $activeTab === 'add-doc' ? 'active' : ''; ?>" id="add-doc">
                <div class="panel-head">
                    <h2>Register New Physician</h2>
                    <p>Create credentials, set consultancy fees, and upload a profile picture</p>
                </div>
                <form method="POST" enctype="multipart/form-data" class="form-stack">
                    <div class="field-group">
                        <label>Doctor Name *</label>
                        <input type="text" name="doctor" class="input-editorial" placeholder="e.g., Santos" required>
                    </div>
                    <div class="field-group">
                        <label>Email ID *</label>
                        <input type="email" name="demail" class="input-editorial" placeholder="doctor@tamarawhospital.com" required>
                    </div>
                    <div class="field-group">
                        <label>Password *</label>
                        <input type="password" name="dpassword" class="input-editorial" placeholder="••••••••" required>
                    </div>
                    <div class="field-group">
                        <label>Consultancy Fees (PHP) *</label>
                        <input type="number" name="docFees" class="input-editorial" placeholder="500" required>
                    </div>
                    <div class="field-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_pic" class="input-editorial" accept="image/*">
                    </div>
                    <div>
                        <button type="submit" name="docsub" class="btn-tactile btn-green">Add Doctor &rarr;</button>
                    </div>
                </form>
            </div>

            <!-- 5. Edit Doctor Tab (Dynamically loaded when clicking Edit) -->
            <div class="tab-panel <?php echo $activeTab === 'edit-doc' ? 'active' : ''; ?>" id="edit-doc">
                <div class="panel-head">
                    <h2>Edit Physician Account</h2>
                    <p>Modify doctor information or replace their profile picture</p>
                </div>
                <?php
                if (isset($_GET['edit_id'])) {
                    $edit_id = intval($_GET['edit_id']);
                    $edit_res = mysqli_query($con, "SELECT * FROM doctreg WHERE id='$edit_id'");
                    if ($doc_data = mysqli_fetch_assoc($edit_res)) {
                ?>
                <form method="POST" enctype="multipart/form-data" class="form-stack">
                    <input type="hidden" name="edit_id" value="<?php echo $doc_data['id']; ?>">
                    <div class="field-group">
                        <label>Doctor Name *</label>
                        <input type="text" name="doctor" class="input-editorial" value="<?php echo htmlspecialchars($doc_data['username']); ?>" required>
                    </div>
                    <div class="field-group">
                        <label>Email ID *</label>
                        <input type="email" name="demail" class="input-editorial" value="<?php echo htmlspecialchars($doc_data['email']); ?>" required>
                    </div>
                    <div class="field-group">
                        <label>Password *</label>
                        <input type="text" name="dpassword" class="input-editorial" value="<?php echo htmlspecialchars($doc_data['password']); ?>" required>
                    </div>
                    <div class="field-group">
                        <label>Consultancy Fees (PHP) *</label>
                        <input type="number" name="docFees" class="input-editorial" value="<?php echo htmlspecialchars($doc_data['docFees']); ?>" required>
                    </div>
                    <div class="field-group">
                        <label>Current Profile Picture</label>
                        <div style="margin-bottom: 8px;">
                            <img src="uploads/doctors/<?php echo !empty($doc_data['profile_pic']) ? htmlspecialchars($doc_data['profile_pic']) : 'default_doctor.png'; ?>" class="doc-avatar" alt="Current Avatar">
                        </div>
                        <label>Replace Profile Picture (Optional)</label>
                        <input type="file" name="profile_pic" class="input-editorial" accept="image/*">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="docedit" class="btn-tactile btn-green">Save Changes</button>
                        <a href="admin_dashboard.php" class="btn-tactile btn-white">Cancel</a>
                    </div>
                </form>
                <?php
                    } else {
                        echo "<p>Doctor not found.</p>";
                    }
                }
                ?>
            </div>

            <!-- 6. Delete Doctor -->
            <div class="tab-panel <?php echo $activeTab === 'del-doc' ? 'active' : ''; ?>" id="del-doc">
                <div class="panel-head">
                    <h2>Remove Physician Account</h2>
                    <p>Permanently revoke doctor access using their registered email address</p>
                </div>
                <form method="POST" class="form-stack" onsubmit="return confirm('Remove this doctor from the registry?');">
                    <div class="field-group">
                        <label>Doctor Email ID *</label>
                        <input type="email" name="demail" class="input-editorial" placeholder="Enter Doctor Email ID" required>
                    </div>
                    <div>
                        <button type="submit" name="docdel" class="btn-tactile btn-danger-tactile">Delete Doctor</button>
                    </div>
                </form>
            </div>

            <!-- 7. Queries / Feedback -->
            <div class="tab-panel <?php echo $activeTab === 'queries' ? 'active' : ''; ?>" id="queries">
                <div class="panel-head">
                    <h2>Contact Inquiries</h2>
                    <p>Messages submitted via the public Contact Us page</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($con, "SELECT * FROM contact");
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr>
                                        <td><strong>" . htmlspecialchars($row['name']) . "</strong></td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>" . htmlspecialchars($row['contact']) . "</td>
                                        <td>" . htmlspecialchars($row['message']) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='empty-state'>No contact messages received yet.</td></tr>";
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