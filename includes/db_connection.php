<?php

$host = "localhost";
$user = "s.lovepreet";  
$password = "sql_lab6";     
$dbname = "gym_portal";

try {
  
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
