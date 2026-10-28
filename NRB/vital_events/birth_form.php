<?php
include '../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $person_id = intval($_POST['person_id']);
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $birth_place = mysqli_real_escape_string($conn, $_POST['birth_place']);
    $mother_id = !empty($_POST['mother_person_id']) ? intval($_POST['mother_person_id']) : NULL;
    $father_id = !empty($_POST['father_person_id']) ? intval($_POST['father_person_id']) : NULL;
    $region = mysqli_real_escape_string($conn, $_POST['region']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $registration_date = mysqli_real_escape_string($conn, $_POST['registration_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO birth_registration (person_id, registration_number, birth_date, birth_place, mother_person_id, father_person_id, region, postal_code, registration_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssiissss", $person_id, $registration_number, $birth_date, $birth_place, $mother_id, $father_id, $region, $postal_code, $registration_date, $status);

        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='alert alert-success'>Birth registration successfully recorded!</div>";
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
    <title>Birth Registration - CRVS System</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">Civil Registration</p>
                <h1>Birth Registration</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Record Birth Registration</h3>
                <span>Official birth entry</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Select Child (Person Record)</label>
                    <select name="person_id" class="form-control" required>
                        <option value="">-- Choose Child --</option>
                        <?php
                        while ($p = mysqli_fetch_assoc($persons_result)) {
                            echo "<option value='{$p['person_id']}'>{$p['first_name']} {$p['last_name']} (DOB: {$p['date_of_birth']})</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" required placeholder="e.g., BR-2026-0001">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Birth Date</label>
                        <input type="date" name="birth_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Birth Place</label>
                        <input type="text" name="birth_place" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mother (Optional)</label>
                        <select name="mother_person_id" class="form-control">
                            <option value="">-- Choose Mother --</option>
                            <?php
                            mysqli_data_seek($persons_result, 0);
                            while ($p = mysqli_fetch_assoc($persons_result)) {
                                echo "<option value='{$p['person_id']}'>{$p['first_name']} {$p['last_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Father (Optional)</label>
                        <select name="father_person_id" class="form-control">
                            <option value="">-- Choose Father --</option>
                            <?php
                            mysqli_data_seek($persons_result, 0);
                            while ($p = mysqli_fetch_assoc($persons_result)) {
                                echo "<option value='{$p['person_id']}'>{$p['first_name']} {$p['last_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Registration Date</label>
                        <input type="date" name="registration_date" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Pending">Pending</option>
                        <option value="Verified">Verified</option>
                        <option value="Approved">Approved</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Birth Record</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    </div>
</div>
</body>
</html>