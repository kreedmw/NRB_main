CREATE DATABASE IF NOT EXISTS nrb_db;
USE nrb_db;

-- 1. Person Table
CREATE TABLE person (
    person_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    sex VARCHAR(10) NOT NULL,
    nationality VARCHAR(50) NOT NULL,
    timestamp_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Registration Office Table
CREATE TABLE registration_office (
    office_id INT AUTO_INCREMENT PRIMARY KEY,
    office_code VARCHAR(50) UNIQUE NOT NULL,
    office_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    region VARCHAR(50) NOT NULL,
    office_type VARCHAR(50) NOT NULL,
    active BOOLEAN DEFAULT TRUE
);

-- 3. Officer Table
CREATE TABLE officer (
    officer_id INT AUTO_INCREMENT PRIMARY KEY,
    office_id INT NOT NULL,
    officer_number VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (office_id) REFERENCES registration_office(office_id) ON DELETE CASCADE
);

-- 4. Birth Registration Table
CREATE TABLE birth_registration (
    birth_registration_id INT AUTO_INCREMENT PRIMARY KEY,
    person_id INT NOT NULL,
    registration_number VARCHAR(50) UNIQUE NOT NULL,
    birth_date DATE NOT NULL,
    birth_place VARCHAR(100) NOT NULL,
    mother_person_id INT,
    father_person_id INT,
    region VARCHAR(50),
    postal_code VARCHAR(20),
    registration_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE,
    FOREIGN KEY (mother_person_id) REFERENCES person(person_id) ON DELETE SET NULL,
    FOREIGN KEY (father_person_id) REFERENCES person(person_id) ON DELETE SET NULL
);

-- 5. Death Registration Table
CREATE TABLE death_registration (
    death_registration_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(50) UNIQUE NOT NULL,
    person_id INT NOT NULL,
    date_of_death DATE NOT NULL,
    place_of_death VARCHAR(100) NOT NULL,
    cause_of_death VARCHAR(255) NOT NULL,
    registration_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE
);

-- 6. Marriage Registration Table
CREATE TABLE marriage_registration (
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
);

-- 7. Identity Document Table
CREATE TABLE identity_document (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    person_id INT NOT NULL,
    document_type VARCHAR(50) NOT NULL, -- e.g., National ID, Birth Certificate
    document_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date DATE NOT NULL,
    expiry_date DATE,
    issuing_office_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'Active',
    FOREIGN KEY (person_id) REFERENCES person(person_id) ON DELETE CASCADE,
    FOREIGN KEY (issuing_office_id) REFERENCES registration_office(office_id)
);

-- 8. Registration Application Table
CREATE TABLE registration_application (
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
);

-- 9. Supporting Document Table
CREATE TABLE supporting_document (
    supporting_document_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    document_number VARCHAR(50) NOT NULL,
    verified_by_office_id INT NOT NULL,
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    verification_status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (application_id) REFERENCES registration_application(application_id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by_office_id) REFERENCES registration_office(office_id)
);