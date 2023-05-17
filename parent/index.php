<?php
session_start();
include('../../config.php');
$id = $_COOKIE['id'];
header("location: $rootURL/parent/attendance_logs.php?id=$id");
include('../logout.php');
