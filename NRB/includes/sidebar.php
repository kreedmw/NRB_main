<?php
$projectRoot = str_replace('\\', '/', dirname(__DIR__));
$documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
$baseUrl = $documentRoot !== '' ? str_replace($documentRoot, '', $projectRoot) : '';
$baseUrl = rtrim($baseUrl, '/');

function nrb_url($path) {
    global $baseUrl;
    return $baseUrl . '/' . ltrim($path, '/');
}
?>

<div class="dashboard-shell">
    <aside class="sidebar">
        <div class="sidebar__brand">
            <span class="brand-mark">NRB</span>
            <div>
                <strong>National Registry</strong>
            </div>
        </div>

        <nav class="sidebar__nav" aria-label="Main navigation">
            <a href="<?= nrb_url('index.php') ?>" class="nav-item active">
                <span>🏠</span>
                Dashboard
            </a>
            <a href="<?= nrb_url('persons/add_person.php') ?>" class="nav-item">
                <span>👤</span>
                Add Person
            </a>
            <a href="<?= nrb_url('offices/index.php') ?>" class="nav-item">
                <span>🏢</span>
                Offices
            </a>
            <a href="<?= nrb_url('officers/index.php') ?>" class="nav-item">
                <span>🧑‍💼</span>
                Officers
            </a>
            <a href="<?= nrb_url('applications/index.php') ?>" class="nav-item">
                <span>📝</span>
                Applications
            </a>
            <a href="<?= nrb_url('documents/index.php') ?>" class="nav-item">
                <span>🪪</span>
                Documents
            </a>
            <a href="<?= nrb_url('vital_events/birth_form.php') ?>" class="nav-item">
                <span>👶</span>
                Birth Record
            </a>
            <a href="<?= nrb_url('vital_events/death_form.php') ?>" class="nav-item">
                <span>⚰️</span>
                Death Record
            </a>
            <a href="<?= nrb_url('vital_events/marriage_form.php') ?>" class="nav-item">
                <span>💍</span>
                Marriage Record
            </a>
            <a href="<?= nrb_url('records/index.php') ?>" class="nav-item">
                <span>📋</span>
                Records
            </a>
        </nav>
    </aside>

    <div class="page-content">
