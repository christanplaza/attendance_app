<?php
session_start();
include('../../config.php');

$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_GET['id'])) {
    if ($conn) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'parent'";

        $parent_res = mysqli_query($conn, $sql);
        $parent = mysqli_fetch_assoc($parent_res);
        $student_id = $parent['student_id'];

        $sql = "SELECT * FROM users WHERE id = '$student_id' AND role = 'student'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);

        $search_query = "";
        if (isset($_GET['search'])) {
            $search_query = $_GET['search'];
            if (!empty($search_query)) {
                $search_query = mysqli_real_escape_string($conn, $_GET['search']);
                $search_query = "AND (t.last_name LIKE '%$search_query%' OR t.first_name LIKE '%$search_query%' OR c.title LIKE '%$search_query%')";
            }
        }

        // Check if sort query is submitted
        $sort_query = "";
        $sort_field = "";
        if (isset($_GET['sort'])) {
            $sort_field = $_GET['sort'];
            if (!empty($sort_field)) {
                if ($sort_field == 'created_at_short') {
                    $sort_field = 'a.created_at';
                }
                $sort_query = "ORDER BY $sort_field ASC;";
            }
        }

        $sql = "SELECT DATE_FORMAT(a.created_at, '%m/%d/%Y') AS created_at_short, c.title, CONCAT_WS(', ', t.last_name, t.first_name) AS teacher_name, CONCAT_WS(' ', DAYNAME(sc.time_start), TIME_FORMAT(sc.time_start, '%h:%i%p'), '-', TIME_FORMAT(sc.time_end, '%h:%i%p')) AS schedule_time 
            FROM attendance a 
            JOIN users s ON a.student_id = s.id 
            JOIN classes c ON a.class_id = c.id 
            JOIN users t ON c.teacher_id = t.id 
            JOIN schedules sc ON a.schedule_id = sc.id 
            WHERE 1 $search_query $sort_query AND a.student_id = '$student_id'";
        $result = mysqli_query($conn, $sql);
    } else {
        echo "Couldn't connect to database.";
    }
}

include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
    <title>Attendance App | Parent</title>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="display-6">Attendance Logs</div>
                        <div class="row mt-4">
                            <table class="table table-striped mt-5">
                                <thead>
                                    <tr>
                                        <th scope="col">Date Created</th>
                                        <!-- <th scope="col">Teacher Name</th> -->
                                        <th scope="col">Class Name</th>
                                        <th scope="col">Schedule Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch the results and print them in a table row
                                    while ($row = $result->fetch_assoc()) {
                                        $title = $row["title"];
                                        $teacher_name = $row["teacher_name"];
                                        $schedule_time = $row["schedule_time"];
                                    ?>
                                        <tr>
                                            <td><?= $row["created_at_short"] ?></td>
                                            <!-- <td><?= $teacher_name ?></td> -->
                                            <td><?= $title ?></td>
                                            <td><?= $schedule_time ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>