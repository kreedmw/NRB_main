<?php
include '../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $spouse1_id = intval($_POST['spouse1_person_id']);
    $spouse2_id = intval($_POST['spouse2_person_id']);
    $marriage_date = mysqli_real_escape_string($conn, $_POST['marriage_date']);
    $marriage_place = mysqli_real_escape_string($conn, $_POST['marriage_place']);
    $registration_date = mysqli_real_escape_string($conn, $_POST['registration_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($spouse1_id === $spouse2_id) {
        $message = "<div class='alert alert-danger'>Error: Spouse 1 and Spouse 2 cannot be the same person.</div>";
    } else {
        $query = "INSERT INTO marriage_registration (registration_number, spouse1_person_id, spouse2_person_id, marriage_date, marriage_place, registration_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "siissss", $registration_number, $spouse1_id, $spouse2_id, $marriage_date, $marriage_place, $registration_date, $status);

            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='alert alert-success'>Marriage registration successfully recorded!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "<div class='alert alert-danger'>Prepare Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

$persons_result = mysqli_query($conn, "SELECT person_id, first_name, last_name, date_of_birth FROM person");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marriage Registration - CRVS System</title>
    
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">Civil Registration</p>
                <h1>Marriage Registration</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Record Marriage Registration</h3>
                <span>Spousal relationship entry</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" required placeholder="e.g., MR-2026-0001">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Spouse 1</label>
                        <select name="spouse1_person_id" class="form-control" required>
                            <option value="">-- Choose Spouse 1 --</option>
                            <?php
                            while ($p = mysqli_fetch_assoc($persons_result)) {
                                echo "<option value='{$p['person_id']}'>{$p['first_name']} {$p['last_name']} (DOB: {$p['date_of_birth']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Spouse 2</label>
                        <select name="spouse2_person_id" class="form-control" required>
                            <option value="">-- Choose Spouse 2 --</option>
                            <?php
                            mysqli_data_seek($persons_result, 0);
                            while ($p = mysqli_fetch_assoc($persons_result)) {
                                echo "<option value='{$p['person_id']}'>{$p['first_name']} {$p['last_name']} (DOB: {$p['date_of_birth']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Marriage Date</label>
                        <input type="date" name="marriage_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Marriage Place</label>
                        <input type="text" name="marriage_place" class="form-control" required placeholder="e.g., City Registry / Venue">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Registration Date</label>
                        <input type="date" name="registration_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Verified">Verified</option>
                            <option value="Approved">Approved</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Save Marriage Record</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    </div>
</div>
</body>
</html>
