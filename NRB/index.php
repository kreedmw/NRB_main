<?php
require 'config/db.php';

$stats = [
    'Persons' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM person'))['total'],
    'Offices' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM registration_office'))['total'],
    'Officers' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM officer'))['total'],
    'Births' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM birth_registration'))['total'],
    'Deaths' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM death_registration'))['total'],
    'Marriages' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM marriage_registration'))['total'],
    'Applications' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM registration_application'))['total'],
    'Documents' => mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM identity_document'))['total'],
];

$quickLinks = [
    ['label' => 'Register Person', 'url' => 'persons/add_person.php', 'icon' => '👤', 'tone' => 'primary'],
    ['label' => 'Add Office', 'url' => 'offices/index.php', 'icon' => '🏢', 'tone' => 'info'],
    ['label' => 'Add Officer', 'url' => 'officers/index.php', 'icon' => '🧑‍💼', 'tone' => 'success'],
    ['label' => 'Birth Record', 'url' => 'vital_events/birth_form.php', 'icon' => '👶', 'tone' => 'success'],
    ['label' => 'Death Record', 'url' => 'vital_events/death_form.php', 'icon' => '⚰️', 'tone' => 'danger'],
    ['label' => 'Marriage Record', 'url' => 'vital_events/marriage_form.php', 'icon' => '💍', 'tone' => 'warning'],
    ['label' => 'Applications', 'url' => 'applications/index.php', 'icon' => '📝', 'tone' => 'primary'],
    ['label' => 'Documents', 'url' => 'documents/index.php', 'icon' => '🪪', 'tone' => 'success'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NRB Dashboard</title>
    <link rel="stylesheet" href="assets/stylesheet.css">
</head>
<body class="app-body">
    <?php include 'includes/sidebar.php'; ?>

    <header class="topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="eyebrow">National Registration Bureau</p>
                <h1>NRB Dashboard</h1>
            </div>
            <span class="status-pill">System Ready</span>
        </div>
    </header>

    <main class="container py-5">
        <section class="hero-card mb-4">
            <div>
                <p class="eyebrow">Civil Registration & Registry Management</p>
                <h2>Manage registrations with a single view</h2>
            </div>
            <a href="persons/add_person.php" class="btn btn-light">Register Person</a>
        </section>

        <section class="stats-grid mb-4">
            <?php foreach ($stats as $label => $value): ?>
                <div class="stat-card">
                    <div class="stat-card__label"><?= htmlspecialchars($label) ?></div>
                    <div class="stat-card__value"><?= htmlspecialchars($value) ?></div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="panel mb-4">
            <div class="panel-head">
                <h3>Quick Access</h3>
                <span>Core NRB operations</span>
            </div>

            <div class="quick-grid">
                <?php foreach ($quickLinks as $link): ?>
                    <a href="<?= htmlspecialchars($link['url']) ?>" class="quick-link <?= htmlspecialchars($link['tone']) ?>">
                        <span class="quick-link__icon"><?= htmlspecialchars($link['icon']) ?></span>
                        <span><?= htmlspecialchars($link['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card primary">
                    <div class="feature-card__icon">👤</div>
                    <h5>Persons</h5>
                    <p>Register a new person record and maintain the citizen profile.</p>
                    <a href="persons/add_person.php" class="btn btn-primary w-100">Add Person</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card primary">
                    <div class="feature-card__icon">🏢</div>
                    <h5>Offices</h5>
                    <p>Manage registration offices and their regional coverage.</p>
                    <a href="offices/index.php" class="btn btn-primary w-100">View Offices</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card primary">
                    <div class="feature-card__icon">🧑‍💼</div>
                    <h5>Officers</h5>
                    <p>Assign officers to office records and roles.</p>
                    <a href="officers/index.php" class="btn btn-primary w-100">View Officers</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card success">
                    <div class="feature-card__icon">👶</div>
                    <h5>Birth</h5>
                    <p>Create and track birth registrations from the registry office.</p>
                    <a href="vital_events/birth_form.php" class="btn btn-success w-100">Birth Form</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card danger">
                    <div class="feature-card__icon">⚰️</div>
                    <h5>Death</h5>
                    <p>Record death events and official registration details.</p>
                    <a href="vital_events/death_form.php" class="btn btn-danger w-100">Death Form</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card warning">
                    <div class="feature-card__icon">💍</div>
                    <h5>Marriage</h5>
                    <p>Register marriages and maintain spouse relationship details.</p>
                    <a href="vital_events/marriage_form.php" class="btn btn-warning w-100 text-white">Marriage Form</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card primary">
                    <div class="feature-card__icon">📝</div>
                    <h5>Applications</h5>
                    <p>Track registration applications submitted by citizens.</p>
                    <a href="applications/index.php" class="btn btn-primary w-100">Open Applications</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-card success">
                    <div class="feature-card__icon">🪪</div>
                    <h5>Documents</h5>
                    <p>Issue identity records and review supporting document entries.</p>
                    <a href="documents/index.php" class="btn btn-success w-100">Issue Documents</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
