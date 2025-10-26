<?php
// config/db_connect.php
// Centralized DB connection for the project

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'final_project';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    // In dev show error; in production log and show generic message
    error_log('DB connect error: ' . $conn->connect_error);
    die("Database connection error.");
}
$conn->set_charset('utf8mb4');
