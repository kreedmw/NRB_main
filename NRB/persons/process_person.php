<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect and sanitize input data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);

    // 2. Prepare the SQL statement to prevent SQL injection
    $query = "INSERT INTO person (first_name, middle_name, last_name, date_of_birth, sex, nationality) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $middle_name, $last_name, $dob, $sex, $nationality);
        
        if (mysqli_stmt_execute($stmt)) {
            $last_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            // Redirect back to form with success message & ID
            header("Location: add_person.php?status=success&id=" . $last_id);
            exit();
        } else {
            $error = urlencode(mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: add_person.php?status=error&msg=" . $error);
            exit();
        }
    } else {
        $error = urlencode(mysqli_error($conn));
        mysqli_close($conn);
        header("Location: add_person.php?status=error&msg=" . $error);
        exit();
    }
} else {
    // If accessed directly without POST, redirect back to form
    header("Location: add_person.php");
    exit();
}
?>