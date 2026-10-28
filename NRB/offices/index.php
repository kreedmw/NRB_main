<?php
require '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $office_code = mysqli_real_escape_string($conn, trim($_POST['office_code'] ?? ''));
    $office_name = mysqli_real_escape_string($conn, trim($_POST['office_name'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $region = mysqli_real_escape_string($conn, trim($_POST['region'] ?? ''));
    $office_type = mysqli_real_escape_string($conn, trim($_POST['office_type'] ?? ''));
    $active = isset($_POST['active']) ? 1 : 0;

    if ($office_code === '' || $office_name === '' || $address === '' || $region === '' || $office_type === '') {
        $message = "<div class='alert alert-danger'>All office fields are required.</div>";
    } else {
        $query = "INSERT INTO registration_office (office_code, office_name, address, region, office_type, active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssi', $office_code, $office_name, $address, $region, $office_type, $active);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='alert alert-success'>Registration office saved successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "<div class='alert alert-danger'>Prepare Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

$offices = mysqli_query($conn, "SELECT * FROM registration_office ORDER BY office_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offices | NRB</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>Registration Offices</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Add Office</h3>
                <span>Regional registration unit</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Office Code</label>
                        <input type="text" name="office_code" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Office Name</label>
                        <input type="text" name="office_name" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Office Type</label>
                        <input type="text" name="office_type" class="form-control" required placeholder="e.g., District, Regional">
                    </div>
                    <div class="col-md-12">
                        <label class="form-check-label" for="active">Active</label>
                        <input type="checkbox" id="active" name="active" checked>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Office</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="panel form-panel mt-4">
            <div class="panel-head">
                <h3>Office List</h3>
                <span>Available offices</span>
            </div>

            <div class="table-responsive">
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Region</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($office = mysqli_fetch_assoc($offices)): ?>
                            <tr>
                                <td><?= htmlspecialchars($office['office_id']) ?></td>
                                <td><?= htmlspecialchars($office['office_code']) ?></td>
                                <td><?= htmlspecialchars($office['office_name']) ?></td>
                                <td><?= htmlspecialchars($office['region']) ?></td>
                                <td><?= htmlspecialchars($office['office_type']) ?></td>
                                <td>
                                    <span class="status-badge <?= ($office['active'] ? 'approved' : 'pending') ?>"><?= $office['active'] ? 'Active' : 'Inactive' ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
