</head>
<style>
    table, td { border: 1px solid black;}
</style>
</head>
<center><form action="insert_player.php" method="post">
<label for="name">Give me your first and last name</label><br>
<input type="text" id="name" name="name"><input type="submit" value="submit">
</form>
<a href="mode.php">Go to select players and game mode≤</a>
</center>
<?php include 'login_data.php';


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id, name FROM players");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allPlayers=$stmt->fetchAll();
}   catch(PDOException $e){    
}
foreach($allPlayers as $player){
    echo "<table>";
    echo "<tr><td width='40px'>".$player['id']."</td><td width='200px'>".$player['name']."</td></tr>";
}
$conn = null;



