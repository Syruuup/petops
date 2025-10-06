<?php
session_start();


if (!isset($_SESSION['email'])) {
    header("Location: login.php");  
    exit();
}


$conn = new mysqli("localhost", "root", "", "bdpeneira");

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

?>
