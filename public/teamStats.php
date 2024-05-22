<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team stats</title>
</head>
<body>
<center>
<?php include 'login_data.php';
$id=$_GET['id'];
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT teams.id, sum(tournament_teams.lose) AS team_lose, sum(tournament_teams.win) AS team_win FROM tournament_teams 
    LEFT JOIN teams ON (
    tournament_teams.team_id = teams.id
    ) WHERE team_id = $id");
    $stmt->execute();
    $AllTeamStats = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $teamStats=$stmt->fetchAll();

}   catch(PDOException $e){    
}
echo "<h1>Team number " . $teamStats[0]['id'] .  " scored " . $teamStats[0]['team_win'] . " wins and " . $teamStats[0]['team_lose'] . " losses </h1>";
?>
</body>
</html>