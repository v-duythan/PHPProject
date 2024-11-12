<?php
// Determine the protocol (HTTP or HTTPS)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

// Get the host name
$host = $_SERVER['HTTP_HOST'];

// Define the base URL, ensuring the path to the project root is accurate
define('BASE_URL', $protocol . '://' . $host . '/PHPProject/');
?>
