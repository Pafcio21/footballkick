<head>
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
    $stmt = $conn->prepare("SELECT games.id AS game_id, teams.id AS team_id, games.team1, games.team2, players.name, games.points_team1, games.points_team2 FROM games LEFT JOIN
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
    if ($match[0]['team_id'] === $match[0]['team1']) {
        echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td><input type=hidden name=id value=$id><input type=number name=points_team1[]></td><td> VS </td><td><input type=number name=points_team2[]></td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
    } else {
        echo "<tr><td>".$match[2]['name'].", ".$match[3]['name']."</td><td><input type=hidden name=id value=$id><input type=number name=points_team1[]></td><td> VS </td><td><input type=number name=points_team2[]></td><td>".$match[0]['name'].", ".$match[1]['name']."</td></tr>";
    }
}else{
    if ($match[0]['team_id'] === $match[0]['team1']) {
        echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td align=center><input type=hidden name=id value=$id>" . $match[1]['points_team1'] . "</td><td> VS </td><td align=center>" . $match[2]['points_team2'] . "</td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
    } else {
        echo "<tr><td>".$match[2]['name'].", ".$match[3]['name']."</td><td align=center><input type=hidden name=id value=$id>" . $match[2]['points_team1'] . "</td><td> VS </td><td align=center>" . $match[1]['points_team2'] . "</td><td>".$match[0]['name'].", ".$match[1]['name']."</td></tr>";
    }
}
$a=$a+1;
}

echo "</table>";
?>
<input type=submit value=submit>
<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT SUM(`points_team1`) AS points, team1 FROM games WHERE stage = 'ended' GROUP BY team1 ORDER BY points DESC");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $PointsL=$stmt->fetchAll();
}   catch(PDOException $e){    
}var_dump($PointsL);
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT SUM(`points_team2`) AS points, team2 FROM games WHERE stage = 'ended' GROUP BY team2 ORDER BY points DESC");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $PointsP=$stmt->fetchAll();
}   catch(PDOException $e){    
}var_dump($PointsP);
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT count(*) AS win1, team1 FROM games WHERE stage = 'ended' AND points_team1 > points_team2
    GROUP BY team1 ORDER BY win1");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $WinTeam1=$stmt->fetchAll();
}   catch(PDOException $e){    
}var_dump($WinTeam1);
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT count(*) AS win2, team2 FROM games WHERE stage = 'ended' AND points_team1 < points_team2
    GROUP BY team2 ORDER BY win2");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $WinTeam2=$stmt->fetchAll();
}   catch(PDOException $e){    
}var_dump($WinTeam2);
$result = [];

$t = array_column($WinTeam2, 'team2');

foreach($WinTeam1 as $k => $v) {
    
    $index = array_search($v['team1'], $t);
    $v['win2'] = $index !== FALSE ? $WinTeam2[$index]['win2'] : 0;
    $result[] = $v;

}
$pointsTable = [];

$h = array_column($PointsP, 'team2');

foreach($PointsL as $k => $p) {
    
    $index = array_search($p['team1'], $h);
    $p['points'] = $index !== FALSE ? $WinTeam2[$index]['points'] : 0;
    $pointsTable[] = $p;

}

var_dump($result);
echo "<table>";
echo "<tr><th>Team</th><th>Win on left side</th><th>Lose</th>";
foreach($result as $table){
echo "<tr><td width:100px align=center>" . $table['team1'] . "</td><td align=center width:100px>" . $table['win1']+$table['win2'] . "</td><td width:100px align=center>" . $table['lose'] . "</td></tr>";
}

