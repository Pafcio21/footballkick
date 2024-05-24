<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team stats</title>
</head>
<style>
    table, td { border: 1px solid black;}
</style>
<body>
<center>
<?php include 'login_data.php';
$id=$_GET['id'];
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT teams.id, sum(teams.lose) AS team_lose, sum(teams.win) AS team_win FROM teams 
    WHERE id = $id");
    $stmt->execute();
    $AllTeamStats = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $teamStats=$stmt->fetchAll();

}   catch(PDOException $e){    
}
echo "<h1>Team number " . $teamStats[0]['id'] .  " scored " . $teamStats[0]['team_win'] . " wins and " . $teamStats[0]['team_lose'] . " losses </h1>";
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT games.id AS game_id, teams.id AS team_id, games.team1, games.team2, players.name, games.points_team1, games.points_team2 FROM games LEFT JOIN
    teams ON(
    games.team1 = teams.id
    OR games.team2 = teams.id
    )
    LEFT JOIN players ON (
    teams.player1_id = players.id
    OR teams.player2_id = players.id
    )
    WHERE games.team1 = $id OR games.team2 = $id");
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
echo "<th></th><th></th><th>Team matches " . $id . "</th><th></th><th></th>";
echo "<tr><th>Team1</th><th></th><th></th><th></th><th>Team2</th>";
echo "<tr><th>Attack   Defense</th><th>Points</th><th></th><th>Points</th><th>Defense   Attack</th>";
$a=1;  
foreach($result as $match)
{
if(is_NULL($match[0]['points_team1']) && is_NULL($match[1]['points_team2']) || is_NULL($match[2]['points_team1']) && is_NULL($match[3]['points_team2']))
{
    if ($match[0]['team_id'] === $match[0]['team1']) {
        echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td><input type=hidden name=id value=$id>Match in progress</td><td align=center> VS </td><td>Match in progress</td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
    } else {
        echo "<tr><td>".$match[2]['name'].", ".$match[3]['name']."</td><td><input type=hidden name=id value=$id>Match in progress</td><td align=center> VS </td><td>Match in progress</td><td>".$match[0]['name'].", ".$match[1]['name']."</td></tr>";
    }
}else{
    if ($match[0]['team_id'] === $match[0]['team1']) {
        echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td align=center>" . $match[1]['points_team1'] . "</td><td align=center> VS </td><td align=center>" . $match[2]['points_team2'] . "</td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
    } else {
        echo "<tr><td>".$match[2]['name'].", ".$match[3]['name']."</td><td align=center>" . $match[2]['points_team1'] . "</td><td align=center> VS </td><td align=center>" . $match[1]['points_team2'] . "</td><td>".$match[0]['name'].", ".$match[1]['name']."</td></tr>";
    }
}
$a=$a+1;
}
?>
</body>
</html>