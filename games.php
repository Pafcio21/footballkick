</head>
<style>
    table, td { border: 1px solid black;}
</style>
</head>
<center>
<?php include 'login_data.php';
$id=$_GET['id'];
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT games.id AS game_id, teams.id AS team_id, players.name FROM games LEFT JOIN
    teams ON(
    games.team1 = teams.id
    OR games.team2 = teams.id
    )
    LEFT JOIN players ON (
    teams.player1_id = players.id
    OR teams.player2_id = players.id
    )
    WHERE tournament_id = $id;");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allPlayers=$stmt->fetchAll();
}   catch(PDOException $e){    
}

$result = array();
foreach ($allPlayers as $element) {
$result[$element['game_id']][] = $element;
}

echo "<table>";
foreach($result as $match)
{
    echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td> VS </td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
}
echo "</table>";


