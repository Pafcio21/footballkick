<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking table</title>
</head>
<body>
<center>
    <?php include 'login_data.php';
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT players.name, tournament_teams.team_id, sum(tournament_teams.win) AS win, sum(tournament_teams.lose) AS lose, sum(tournament_teams.points) AS points
        FROM tournament_teams 
        LEFT JOIN teams ON tournament_teams.team_id = teams.id 
        LEFT JOIN players ON teams.player1_id = players.id OR teams.player2_id = players.id 
        GROUP BY players.name 
        ORDER BY win DESC, points DESC, lose ASC");
        $stmt->execute();
        $AllTeamStats = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $team_stats=$stmt->fetchAll();
    
    }   catch(PDOException $e){    
    }
    $table = array();
    foreach ($team_stats as $stats) {
        $table[$stats['name']][] = $stats;
    }
        echo "<h1>Players Scoreboard</h1>";
        echo "<table border='1'>";
        echo "<tr><th>Position</th><th>Players Name</th><th>Wins</th><th>Lost</th><th>Points</th></tr>";
        $position = 1;
        foreach($table as $team){
            echo "<tr>";
            echo "<td align=center>" . $position . "</td>";
            echo "<td align=center>" . $team[0]['name'] . "</td>";
            echo "<td align=center>" . $team[0]['win'] . "</td>";
            echo "<td align=center>" . $team[0]['lose'] . "</td>";
            echo "<td align=center>" . $team[0]['points'] . "</td>";
            echo "</tr>";
            $position++;
        }
    ?>
</center>
</body>
</html>