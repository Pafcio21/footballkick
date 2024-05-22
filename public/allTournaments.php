</head>
<style>
    table, td { border: 1px solid black;}
</style>
<title>See tournaments</title>
</head>
<center>
<?php include 'login_data.php';
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id, name FROM tournaments");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allNames=$stmt->fetchAll();
}   catch(PDOException $e){    
}
echo "Choose tournament:";
echo "<table>";
$a=1;
foreach($allNames as $name){
    $id=$name['id'];
    echo "<tr><td width='40px'><a href=games.php?id=$id>".$a."</a></td><td width='200px'><a href=games.php?id=$id>".$name['name']."</a></td></tr>";
    $a++;
}