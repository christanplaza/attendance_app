<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if student ID is set
if (isset($_GET['id'])) {
    // Get student ID from GET parameter
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch student information
    $sql = "SELECT first_name, last_name, unique_code FROM users WHERE id = '$student_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    // Check if query was successful
    if ($result) {
        // Check if student was found
        if (mysqli_num_rows($result) > 0) {
            // Fetch student information
            $row = mysqli_fetch_assoc($result);

            // Build response object
            $response = array(
                'status' => 'success',
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'unique_code' => $row['unique_code']
            );
        } else {
            // Student not found
            $response = array(
                'status' => 'error',
                'message' => 'Student not found'
            );
        }
    } else {
        // Query failed
        $response = array(
            'status' => 'error',
            'message' => 'Query failed: ' . mysqli_error($conn)
        );
    }
} else {
    // Student ID not set
    $response = array(
        'status' => 'error',
        'message' => 'Student ID not set'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
