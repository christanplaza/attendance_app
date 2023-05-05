<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get student code from API request
$parent_id = mysqli_real_escape_string($conn, $_POST['parent_id']);

$sql = "SELECT * FROM users WHERE id = '$parent_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Get student_id from result
    $row = mysqli_fetch_assoc($result);
    $student_id = $row['student_id'];

    if ($student_id) {
        $response = array(
            'status' => 'success',
            'student_id' => $student_id
        );
    } else {
        $response = array(
            'status' => 'null'
        );
    }


    // $sql = "SELECT COUNT(*) as cnt FROM users WHERE student_id = '$student_id'";
    // $result = mysqli_query($conn, $sql);
    // $student_count = mysqli_fetch_assoc($result);

    // if ($student_count['cnt'] > 0) {
    //     // Student is already paired with a parent, return error message
    //     $response = array(
    //         'status' => 'warning',
    //         'message' => 'Student is already paired with a parent',
    //         'student' => $row
    //     );
    // } else {
    //     // Return success message
    //     $response = array(
    //         'status' => 'success',
    //         'message' => 'Valid Student Code',
    //         'student' => $row
    //     );
    // }
} else {
    // Return error message if no student was found
    $response = array(
        'status' => 'error',
        'message' => 'Invalid ID'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
