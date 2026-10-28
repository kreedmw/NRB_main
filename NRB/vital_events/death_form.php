<?php
include '../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $person_id = intval($_POST['person_id']);
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $date_of_death = mysqli_real_escape_string($conn, $_POST['date_of_death']);
    $place_of_death = mysqli_real_escape_string($conn, $_POST['place_of_death']);
    $cause_of_death = mysqli_real_escape_string($conn, $_POST['cause_of_death']);
    $registration_date = mysqli_real_escape_string($conn, $_POST['registration_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO death_registration (registration_number, person_id, date_of_death, place_of_death, cause_of_death, registration_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sisssss", $registration_number, $person_id, $date_of_death, $place_of_death, $cause_of_death, $registration_date, $status);

        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='alert alert-success'>Death registration successfully recorded!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<div class='alert alert-danger'>Prepare Error: " . mysqli_error($conn) . "</div>";
    }
}

$persons_result = mysqli_query($conn, "SELECT person_id, first_name, last_name, date_of_birth FROM person");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Death Registration - CRVS System</title>
    
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">Civil Registration</p>
                <h1>Death Registration</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Record Death Registration</h3>
                <span>Vital event record</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Select Deceased Person</label>
                    <select name="person_id" class="form-control" required>
                        <option value="">-- Choose Person --</option>
                        <?php
                        while ($p = mysqli_fetch_assoc($persons_result)) {
                            echo "<option value='{$p['person_id']}'>{$p['first_name']} {$p['last_name']} (DOB: {$p['date_of_birth']})</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" required placeholder="e.g., DR-2026-0001">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Death</label>
                        <input type="date" name="date_of_death" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Place of Death</label>
                        <input type="text" name="place_of_death" class="form-control" required placeholder="e.g., Hospital / City">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cause of Death</label>
                    <input type="text" name="cause_of_death" class="form-control" required placeholder="e.g., Natural causes">
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
                    <button type="submit" class="btn btn-danger">Save Death Record</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    </div>
</div>
</body>
</html>