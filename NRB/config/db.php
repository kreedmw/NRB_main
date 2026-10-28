<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'nrb_db';

$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$sql_db = "CREATE DATABASE IF NOT EXISTS `$dbname`";
mysqli_query($conn, $sql_db);

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database Selection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

$tables = [
    "person" => "CREATE TABLE IF NOT EXISTS person (
        person_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        middle_name VARCHAR(100) DEFAULT NULL,
        last_name VARCHAR(100) NOT NULL,
        date_of_birth DATE NOT NULL,
        sex VARCHAR(10) NOT NULL,
        nationality VARCHAR(50) NOT NULL,
        timestamp_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "registration_office" => "CREATE TABLE IF NOT EXISTS registration_office (
        office_id INT AUTO_INCREMENT PRIMARY KEY,
        office_code VARCHAR(50) UNIQUE NOT NULL,
        office_name VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        region VARCHAR(50) NOT NULL,
        office_type VARCHAR(50) NOT NULL,
        active BOOLEAN DEFAULT TRUE
    )",

    "officer" => "CREATE TABLE IF NOT EXISTS officer (
        officer_id INT AUTO_INCREMENT PRIMARY KEY,
        office_id INT NOT NULL,
        officer_number VARCHAR(50) UNIQUE NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        role VARCHAR(50) NOT NULL,
        active BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (office_id) REFERENCES registration_office(office_id) ON DELETE CASCADE
    )",

    "birth_registration" => "CREATE TABLE IF NOT EXISTS birth_registration (
        birth_registration_id INT AUTO_INCREMENT PRIMARY KEY,
        person_id INT NOT NULL,
        registration_number VARCHAR(50) UNIQUE NOT NULL,
        birth_date DATE NOT NULL,
        birth_place VARCHAR(100) NOT NULL,
        mother_person_id INT DEFAULT NULL,
        father_person_id INT DEFAULT NULL,
        region VARCHAR(50) DEFAULT NULL,
        postal_code VARCHAR(20) DEFAULT NULL,
        registration_date DATE NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE,
        FOREIGN KEY (mother_person_id) REFERENCES person(person_id) ON DELETE SET NULL,
        FOREIGN KEY (father_person_id) REFERENCES person(person_id) ON DELETE SET NULL
    )",

    "death_registration" => "CREATE TABLE IF NOT EXISTS death_registration (
        death_registration_id INT AUTO_INCREMENT PRIMARY KEY,
        registration_number VARCHAR(50) UNIQUE NOT NULL,
        person_id INT NOT NULL,
        date_of_death DATE NOT NULL,
        place_of_death VARCHAR(100) NOT NULL,
        cause_of_death VARCHAR(255) NOT NULL,
        registration_date DATE NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE
    )",

    "marriage_registration" => "CREATE TABLE IF NOT EXISTS marriage_registration (
        marriage_registration_id INT AUTO_INCREMENT PRIMARY KEY,
        registration_number VARCHAR(50) UNIQUE NOT NULL,
        spouse1_person_id INT NOT NULL,
        spouse2_person_id INT NOT NULL,
        marriage_date DATE NOT NULL,
        marriage_place VARCHAR(100) NOT NULL,
        registration_date DATE NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        FOREIGN KEY (spouse1_person_id) REFERENCES person(person_id) ON DELETE CASCADE,
        FOREIGN KEY (spouse2_person_id) REFERENCES person(person_id) ON DELETE CASCADE
    )",

    "identity_document" => "CREATE TABLE IF NOT EXISTS identity_document (
        document_id INT AUTO_INCREMENT PRIMARY KEY,
        person_id INT NOT NULL,
        document_type VARCHAR(50) NOT NULL,
        document_number VARCHAR(50) UNIQUE NOT NULL,
        issue_date DATE NOT NULL,
        expiry_date DATE DEFAULT NULL,
        issuing_office_id INT NOT NULL,
        status VARCHAR(50) DEFAULT 'Active',
        FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE,
        FOREIGN KEY (issuing_office_id) REFERENCES registration_office(office_id)
    )",

    "registration_application" => "CREATE TABLE IF NOT EXISTS registration_application (
        application_id INT AUTO_INCREMENT PRIMARY KEY,
        person_id INT NOT NULL,
        office_id INT NOT NULL,
        officer_id INT NOT NULL,
        application_type VARCHAR(50) NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        processed_at TIMESTAMP NULL,
        status VARCHAR(50) DEFAULT 'Submitted',
        remarks TEXT,
        FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE,
        FOREIGN KEY (office_id) REFERENCES registration_office(office_id),
        FOREIGN KEY (officer_id) REFERENCES officer(officer_id)
    )",

    "supporting_document" => "CREATE TABLE IF NOT EXISTS supporting_document (
        supporting_document_id INT AUTO_INCREMENT PRIMARY KEY,
        application_id INT NOT NULL,
        document_type VARCHAR(50) NOT NULL,
        document_number VARCHAR(50) NOT NULL,
        verified_by_office_id INT NOT NULL,
        verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        verification_status VARCHAR(50) DEFAULT 'Pending',
        FOREIGN KEY (application_id) REFERENCES registration_application(application_id) ON DELETE CASCADE,
        FOREIGN KEY (verified_by_office_id) REFERENCES registration_office(office_id)
    )"
];

foreach ($tables as $sql) {
    mysqli_query($conn, $sql);
}
?>