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
        $stmt = $conn->prepare("SELECT teams.id, GROUP_CONCAT(players.name) AS player_names, teams.elo, teams.win, teams.lose, teams.points,
        sum(teams.win)-sum(teams.lose) AS ranking 
        FROM teams 
        LEFT JOIN players ON teams.player1_id = players.id OR teams.player2_id = players.id 
        GROUP BY teams.id 
        ORDER BY elo DESC, ranking DESC, points DESC");
        $stmt->execute();
        $AllTeamStats = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $team_stats=$stmt->fetchAll();
    
    }   catch(PDOException $e){    
    }
    $table = array();
    foreach ($team_stats as $stats) {
        $table[$stats['id']][] = $stats;
    }
        echo "<h1>Teams Scoreboard</h1>";
        echo "<table border='1'>";
        echo "<tr><th>Position</th><th>Team ID</th><th>Players Name</th><th>Wins</th><th>Lost</th><th>Points</th><th>ELO</th></tr>";
        $position = 1;
        foreach($table as $team){
            $id = $team[0]['id'];
            echo "<tr>";
            echo "<td align=center>" . $position . ".</td>";
            echo "<td align=center>" . $team[0]['id'] . "</td>";
            echo "<td align=center><a href=teamStats.php?id=$id>" . $team[0]['player_names'] . "</a></td>";
            echo "<td align=center>" . $team[0]['win'] . "</td>";
            echo "<td align=center>" . $team[0]['lose'] . "</td>";
            echo "<td align=center>" . $team[0]['points'] . "</td>";
            echo "<td align=center>" . $team[0]['elo'] . "</td>";
            echo "</tr>";
            $position++;
        }
    ?>
</center>
</body>
</html>