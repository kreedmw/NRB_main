<?php
include '../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);

    $query = "INSERT INTO person (first_name, middle_name, last_name, date_of_birth, sex, nationality) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $middle_name, $last_name, $dob, $sex, $nationality);

        if (mysqli_stmt_execute($stmt)) {
            $last_id = mysqli_insert_id($conn);
            $message = "<div class='alert alert-success'>Person successfully registered! Assigned ID: $last_id</div>";
        } else {
            $message = "<div class='alert alert-danger'>Execution Error: " . mysqli_stmt_error($stmt) . "</div>";
        }

        mysqli_stmt_close($stmt);
    } else {
        $message = "<div class='alert alert-danger'>Prepare Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Person - NRB System</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>Register New Person</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Person Details</h3>
                <span>Official record entry</span>
            </div>

            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] === 'success') {
                    $new_id = intval($_GET['id']);
                    echo "<div class='alert alert-success'>Person successfully registered! Assigned ID: $new_id</div>";
                } elseif ($_GET['status'] === 'error') {
                    $error_msg = htmlspecialchars($_GET['msg']);
                    echo "<div class='alert alert-danger'>Error: $error_msg</div>";
                }
            }

            echo $message;
            ?>

            <form action="process_person.php" method="POST" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sex</label>
                        <select name="sex" class="form-control" required>
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" class="form-control" required>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">Save Person</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    </div>
</div>
</body>
</html>