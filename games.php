</head>
<style>
    table, td { border: 1px solid black;}
</style>
</head>
<form action="insert_points.php" method=post>
<center>
<?php include 'login_data.php';
$id=$_GET['id'];
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT games.id AS game_id, teams.id AS team_id, players.name, games.team_shuffle, games.points_team1, games.points_team2 FROM games LEFT JOIN
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
echo "<tr><th>Team1</th><th></th><th></th><th></th><th>Team2</th>";
echo "<tr><th>Attack   Defense</th><th>Points</th><th></th><th>Points</th><th>Defense   Attack</th>";
$a=1;
foreach($result as $match)
{
if(is_NULL($match[0]['points_team1']) && is_NULL($match[1]['points_team2']) || is_NULL($match[2]['points_team1']) && is_NULL($match[3]['points_team2']))
{
echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td><input type=hidden name=id value=$id><input type=number name=points_team1[$a]></td><td> VS </td><td><input type=number name=points_team2[$a]></td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
}else{
echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td align=center><input type=hidden name=id value=$id>" . $match[1]['points_team1'] . "</td><td> VS </td><td align=center>" . $match[2]['points_team2'] . "</td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
}
$a=$a+1;
}

echo "</table>";
?>
<input type=submit value=submit>


