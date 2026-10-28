<?php
require '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $office_id = intval($_POST['office_id'] ?? 0);
    $officer_number = mysqli_real_escape_string($conn, trim($_POST['officer_number'] ?? ''));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $role = mysqli_real_escape_string($conn, trim($_POST['role'] ?? ''));
    $active = isset($_POST['active']) ? 1 : 0;

    if ($office_id <= 0 || $officer_number === '' || $first_name === '' || $last_name === '' || $role === '') {
        $message = "<div class='alert alert-danger'>All officer fields are required.</div>";
    } else {
        $query = "INSERT INTO officer (office_id, officer_number, first_name, last_name, role, active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'issssi', $office_id, $officer_number, $first_name, $last_name, $role, $active);

            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='alert alert-success'>Officer saved successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
            }

            mysqli_stmt_close($stmt);
        } else {
            $message = "<div class='alert alert-danger'>Prepare Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

$offices = mysqli_query($conn, "SELECT office_id, office_name, office_code FROM registration_office ORDER BY office_name ASC");
$officers = mysqli_query($conn, "
    SELECT o.*, ro.office_name, ro.office_code
    FROM officer o
    JOIN registration_office ro ON ro.office_id = o.office_id
    ORDER BY o.officer_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officers | NRB</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>Officers</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Add Officer</h3>
                <span>Assign officer to an office</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Office</label>
                        <select name="office_id" class="form-control" required>
                            <option value="">-- Choose Office --</option>
                            <?php while ($office = mysqli_fetch_assoc($offices)): ?>
                                <option value="<?= (int) $office['office_id'] ?>"><?= htmlspecialchars($office['office_name']) ?> (<?= htmlspecialchars($office['office_code']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Officer Number</label>
                        <input type="text" name="officer_number" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <input type="text" name="role" class="form-control" required placeholder="Registrar, Supervisor, etc.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-check-label" for="active">Active</label>
                        <input type="checkbox" id="active" name="active" checked>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Officer</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="panel form-panel mt-4">
            <div class="panel-head">
                <h3>Officer List</h3>
                <span>Assigned staff</span>
            </div>

            <div class="table-responsive">
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Officer Number</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Office</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($officer = mysqli_fetch_assoc($officers)): ?>
                            <tr>
                                <td><?= htmlspecialchars($officer['officer_id']) ?></td>
                                <td><?= htmlspecialchars($officer['officer_number']) ?></td>
                                <td><?= htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']) ?></td>
                                <td><?= htmlspecialchars($officer['role']) ?></td>
                                <td><?= htmlspecialchars($officer['office_name']) ?></td>
                                <td>
                                    <span class="status-badge <?= ($officer['active'] ? 'approved' : 'pending') ?>"><?= $officer['active'] ? 'Active' : 'Inactive' ?></span>
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
