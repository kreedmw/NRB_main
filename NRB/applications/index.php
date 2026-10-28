<?php
require '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $person_id = intval($_POST['person_id'] ?? 0);
    $office_id = intval($_POST['office_id'] ?? 0);
    $officer_id = intval($_POST['officer_id'] ?? 0);
    $application_type = mysqli_real_escape_string($conn, trim($_POST['application_type'] ?? ''));
    $status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'Submitted'));
    $remarks = mysqli_real_escape_string($conn, trim($_POST['remarks'] ?? ''));

    if ($person_id <= 0 || $office_id <= 0 || $officer_id <= 0 || $application_type === '') {
        $message = "<div class='alert alert-danger'>Person, office, officer, and application type are required.</div>";
    } else {
        $query = "INSERT INTO registration_application (person_id, office_id, officer_id, application_type, status, remarks) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'iiisss', $person_id, $office_id, $officer_id, $application_type, $status, $remarks);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='alert alert-success'>Registration application submitted successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "<div class='alert alert-danger'>Prepare Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

$persons = mysqli_query($conn, "SELECT person_id, first_name, middle_name, last_name, date_of_birth FROM person ORDER BY first_name ASC");
$offices = mysqli_query($conn, "SELECT office_id, office_name, office_code FROM registration_office ORDER BY office_name ASC");
$officers = mysqli_query($conn, "SELECT officer_id, first_name, last_name, role, office_id FROM officer ORDER BY first_name ASC");
$applications = mysqli_query($conn, "
    SELECT ra.*, p.first_name, p.last_name, ro.office_name, o.first_name AS officer_first_name, o.last_name AS officer_last_name
    FROM registration_application ra
    JOIN person p ON p.person_id = ra.person_id
    JOIN registration_office ro ON ro.office_id = ra.office_id
    JOIN officer o ON o.officer_id = ra.officer_id
    ORDER BY ra.application_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications | NRB</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>Applications</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>New Application</h3>
                <span>Submit a registration request</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Person</label>
                        <select name="person_id" class="form-control" required>
                            <option value="">-- Choose Person --</option>
                            <?php while ($person = mysqli_fetch_assoc($persons)): ?>
                                <option value="<?= $person['person_id'] ?>"><?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Application Type</label>
                        <input type="text" name="application_type" class="form-control" required placeholder="Birth, Death, Marriage, ID">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Office</label>
                        <select name="office_id" class="form-control" required>
                            <option value="">-- Choose Office --</option>
                            <?php while ($office = mysqli_fetch_assoc($offices)): ?>
                                <option value="<?= $office['office_id'] ?>"><?= htmlspecialchars($office['office_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Officer</label>
                        <select name="officer_id" class="form-control" required>
                            <option value="">-- Choose Officer --</option>
                            <?php while ($officer = mysqli_fetch_assoc($officers)): ?>
                                <option value="<?= $officer['officer_id'] ?>"><?= htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name'] . ' - ' . $officer['role']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Submitted">Submitted</option>
                            <option value="Pending">Pending</option>
                            <option value="Verified">Verified</option>
                            <option value="Approved">Approved</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                    <a href="../index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="panel form-panel mt-4">
            <div class="panel-head">
                <h3>Application List</h3>
                <span>Recent submissions</span>
            </div>

            <div class="table-responsive">
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Person</th>
                            <th>Type</th>
                            <th>Office</th>
                            <th>Officer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($application = mysqli_fetch_assoc($applications)): ?>
                            <tr>
                                <td><?= htmlspecialchars($application['application_id']) ?></td>
                                <td><?= htmlspecialchars($application['first_name'] . ' ' . $application['last_name']) ?></td>
                                <td><?= htmlspecialchars($application['application_type']) ?></td>
                                <td><?= htmlspecialchars($application['office_name']) ?></td>
                                <td><?= htmlspecialchars($application['officer_first_name'] . ' ' . $application['officer_last_name']) ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($application['status']) ?>"><?= htmlspecialchars($application['status']) ?></span>
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
