<?php
require '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';

    if ($section === 'identity') {
        $person_id = intval($_POST['person_id'] ?? 0);
        $document_type = mysqli_real_escape_string($conn, trim($_POST['document_type'] ?? ''));
        $document_number = mysqli_real_escape_string($conn, trim($_POST['document_number'] ?? ''));
        $issue_date = mysqli_real_escape_string($conn, trim($_POST['issue_date'] ?? ''));
        $expiry_date = mysqli_real_escape_string($conn, trim($_POST['expiry_date'] ?? ''));
        $issuing_office_id = intval($_POST['issuing_office_id'] ?? 0);
        $status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'Active'));

        if ($person_id <= 0 || $document_type === '' || $document_number === '' || $issue_date === '' || $issuing_office_id <= 0) {
            $message = "<div class='alert alert-danger'>All identity document fields are required.</div>";
        } else {
            $query = "INSERT INTO identity_document (person_id, document_type, document_number, issue_date, expiry_date, issuing_office_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            if ($stmt) {
                $expiry = $expiry_date !== '' ? $expiry_date : null;
                mysqli_stmt_bind_param($stmt, 'issssis', $person_id, $document_type, $document_number, $issue_date, $expiry, $issuing_office_id, $status);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "<div class='alert alert-success'>Identity document issued successfully.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    if ($section === 'supporting') {
        $application_id = intval($_POST['application_id'] ?? 0);
        $document_type = mysqli_real_escape_string($conn, trim($_POST['support_document_type'] ?? ''));
        $document_number = mysqli_real_escape_string($conn, trim($_POST['support_document_number'] ?? ''));
        $verified_by_office_id = intval($_POST['verified_by_office_id'] ?? 0);
        $verification_status = mysqli_real_escape_string($conn, trim($_POST['verification_status'] ?? 'Pending'));

        if ($application_id <= 0 || $document_type === '' || $document_number === '' || $verified_by_office_id <= 0) {
            $message = "<div class='alert alert-danger'>All supporting document fields are required.</div>";
        } else {
            $query = "INSERT INTO supporting_document (application_id, document_type, document_number, verified_by_office_id, verification_status) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'issis', $application_id, $document_type, $document_number, $verified_by_office_id, $verification_status);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "<div class='alert alert-success'>Supporting document recorded successfully.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

$persons = mysqli_query($conn, "SELECT person_id, first_name, last_name FROM person ORDER BY first_name ASC");
$offices = mysqli_query($conn, "SELECT office_id, office_name FROM registration_office ORDER BY office_name ASC");
$applications = mysqli_query($conn, "
    SELECT ra.application_id, p.first_name, p.last_name, ra.application_type
    FROM registration_application ra
    JOIN person p ON p.person_id = ra.person_id
    ORDER BY ra.application_id DESC
");
$identity_documents = mysqli_query($conn, "
    SELECT idoc.*, p.first_name, p.last_name, ro.office_name
    FROM identity_document idoc
    JOIN person p ON p.person_id = idoc.person_id
    JOIN registration_office ro ON ro.office_id = idoc.issuing_office_id
    ORDER BY idoc.document_id DESC
");
$supporting_documents = mysqli_query($conn, "
    SELECT sd.*, ra.application_id, p.first_name, p.last_name, ro.office_name
    FROM supporting_document sd
    JOIN registration_application ra ON ra.application_id = sd.application_id
    JOIN person p ON p.person_id = ra.person_id
    JOIN registration_office ro ON ro.office_id = sd.verified_by_office_id
    ORDER BY sd.supporting_document_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents | NRB</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>Documents</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Issue Identity Document</h3>
                <span>Official identity record</span>
            </div>

            <?= $message ?>

            <form method="POST" class="mt-3">
                <input type="hidden" name="section" value="identity">
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
                        <label class="form-label">Document Type</label>
                        <input type="text" name="document_type" class="form-control" required placeholder="National ID, Birth Certificate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Number</label>
                        <input type="text" name="document_number" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Issuing Office</label>
                        <select name="issuing_office_id" class="form-control" required>
                            <option value="">-- Choose Office --</option>
                            <?php while ($office = mysqli_fetch_assoc($offices)): ?>
                                <option value="<?= $office['office_id'] ?>"><?= htmlspecialchars($office['office_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Active">Active</option>
                            <option value="Expired">Expired</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Issue Document</button>
                </div>
            </form>
        </div>

        <div class="panel form-panel mt-4">
            <div class="panel-head">
                <h3>Verify Supporting Document</h3>
                <span>Application supporting proof</span>
            </div>

            <form method="POST" class="mt-3">
                <input type="hidden" name="section" value="supporting">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Application</label>
                        <select name="application_id" class="form-control" required>
                            <option value="">-- Choose Application --</option>
                            <?php while ($application = mysqli_fetch_assoc($applications)): ?>
                                <option value="<?= $application['application_id'] ?>"><?= htmlspecialchars($application['application_id'] . ' - ' . $application['first_name'] . ' ' . $application['last_name'] . ' (' . $application['application_type'] . ')') ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Type</label>
                        <input type="text" name="support_document_type" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Number</label>
                        <input type="text" name="support_document_number" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Verified By Office</label>
                        <select name="verified_by_office_id" class="form-control" required>
                            <option value="">-- Choose Office --</option>
                            <?php while ($office = mysqli_fetch_assoc($offices)): ?>
                                <option value="<?= $office['office_id'] ?>"><?= htmlspecialchars($office['office_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Verification Status</label>
                        <select name="verification_status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Verified">Verified</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Supporting Document</button>
                </div>
            </form>
        </div>

        <div class="panel form-panel mt-4">
            <div class="panel-head">
                <h3>Issued Documents</h3>
                <span>Identity and supporting records</span>
            </div>

            <div class="table-responsive mb-4">
                <h4>Identity Documents</h4>
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Person</th>
                            <th>Type</th>
                            <th>Number</th>
                            <th>Office</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($doc = mysqli_fetch_assoc($identity_documents)): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['document_id']) ?></td>
                                <td><?= htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']) ?></td>
                                <td><?= htmlspecialchars($doc['document_type']) ?></td>
                                <td><?= htmlspecialchars($doc['document_number']) ?></td>
                                <td><?= htmlspecialchars($doc['office_name']) ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($doc['status']) ?>"><?= htmlspecialchars($doc['status']) ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <h4>Supporting Documents</h4>
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Application</th>
                            <th>Type</th>
                            <th>Number</th>
                            <th>Verified By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($doc = mysqli_fetch_assoc($supporting_documents)): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['supporting_document_id']) ?></td>
                                <td><?= htmlspecialchars($doc['application_id'] . ' - ' . $doc['first_name'] . ' ' . $doc['last_name']) ?></td>
                                <td><?= htmlspecialchars($doc['document_type']) ?></td>
                                <td><?= htmlspecialchars($doc['document_number']) ?></td>
                                <td><?= htmlspecialchars($doc['office_name']) ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($doc['verification_status']) ?>"><?= htmlspecialchars($doc['verification_status']) ?></span>
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
