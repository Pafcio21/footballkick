<?php
include 'login_data.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO players(name) Values('" . $_POST['name'] . "')";
    $conn->exec($sql);
}   catch(PDOException $e){    
}
$conn = null;
header("Location: players.php");
exit();