<?php
require '../config/db.php';

$filter = $_GET['type'] ?? 'all';
$search = strtolower(trim($_GET['search'] ?? ''));

$records = [];

$personRows = mysqli_query($conn, "
    SELECT person_id AS id, CONCAT(first_name, ' ', last_name) AS name, date_of_birth AS event_date, 'Person' AS type, 'Active' AS status
    FROM person
");
while ($row = mysqli_fetch_assoc($personRows)) {
    $records[] = $row;
}

$birthRows = mysqli_query($conn, "
    SELECT br.birth_registration_id AS id,
           CONCAT(p.first_name, ' ', p.last_name) AS name,
           br.birth_date AS event_date,
           'Birth' AS type,
           br.status
    FROM birth_registration br
    JOIN person p ON p.person_id = br.person_id
");
while ($row = mysqli_fetch_assoc($birthRows)) {
    $records[] = $row;
}

$deathRows = mysqli_query($conn, "
    SELECT dr.death_registration_id AS id,
           CONCAT(p.first_name, ' ', p.last_name) AS name,
           dr.date_of_death AS event_date,
           'Death' AS type,
           dr.status
    FROM death_registration dr
    JOIN person p ON p.person_id = dr.person_id
");
while ($row = mysqli_fetch_assoc($deathRows)) {
    $records[] = $row;
}

$marriageRows = mysqli_query($conn, "
    SELECT mr.marriage_registration_id AS id,
           CONCAT(sp1.first_name, ' ', sp1.last_name, ' & ', sp2.first_name, ' ', sp2.last_name) AS name,
           mr.marriage_date AS event_date,
           'Marriage' AS type,
           mr.status
    FROM marriage_registration mr
    JOIN person sp1 ON sp1.person_id = mr.spouse1_person_id
    JOIN person sp2 ON sp2.person_id = mr.spouse2_person_id
");
while ($row = mysqli_fetch_assoc($marriageRows)) {
    $records[] = $row;
}

$filtered = [];
foreach ($records as $record) {
    $typeMatch = $filter === 'all' || strtolower($record['type']) === strtolower($filter);
    $searchText = strtolower((string) $record['name'] . ' ' . (string) $record['id']);
    $searchMatch = $search === '' || strpos($searchText, $search) !== false;

    if ($typeMatch && $searchMatch) {
        $filtered[] = $record;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records | NRB</title>
    <link rel="stylesheet" href="../assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include '../includes/sidebar.php'; ?>

    <header class="topbar small">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>Records Search</h1>
            </div>
            <a href="../index.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
    </header>

    <main class="container py-5">
        <div class="panel form-panel">
            <div class="panel-head">
                <h3>Registered entries</h3>
                <span>Search and filter all records</span>
            </div>

            <form method="GET" class="record-toolbar">
                <div class="input-group record-search">
                    <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search by name or ID">
                </div>

                <div class="d-flex align-items-center gap-2">
                    <select name="type" class="form-control">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Records</option>
                        <option value="person" <?= $filter === 'person' ? 'selected' : '' ?>>Persons</option>
                        <option value="birth" <?= $filter === 'birth' ? 'selected' : '' ?>>Births</option>
                        <option value="death" <?= $filter === 'death' ? 'selected' : '' ?>>Deaths</option>
                        <option value="marriage" <?= $filter === 'marriage' ? 'selected' : '' ?>>Marriages</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($filtered) > 0): ?>
                            <?php foreach ($filtered as $record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['type']) ?></td>
                                    <td><?= htmlspecialchars($record['id']) ?></td>
                                    <td><?= htmlspecialchars($record['name']) ?></td>
                                    <td><?= htmlspecialchars($record['event_date']) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower($record['status']) ?>"><?= htmlspecialchars($record['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No matching records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    </div>
</div>
</body>
</html>
