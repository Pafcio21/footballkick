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
            echo "<tr><td>".$match[0]['name'].", ".$match[1]['name']."</td><td align=center>" . $match[1]['points_team1'] . "</td><td> VS </td><td align=center>" . $match[2]['points_team2'] . "</td><td>".$match[2]['name'].", ".$match[3]['name']."</td></tr>";
        } else {
            echo "<tr><td>".$match[2]['name'].", ".$match[3]['name']."</td><td align=center>" . $match[2]['points_team1'] . "</td><td> VS </td><td align=center>" . $match[1]['points_team2'] . "</td><td>".$match[0]['name'].", ".$match[1]['name']."</td></tr>";
        }
    }
    $a=$a+1;
    }

    echo "</table>";
    ?>
    <input type=submit value=submit>
    <?php
    $id=$_GET['id'];
        try {
            // Tworzenie połączenia PDO
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Ustawienie trybu błędów PDO na wyjątki
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Pobieranie danych z tabeli games
            $sql = "SELECT points_team1, points_team2, team1, team2 FROM games WHERE tournament_id = $id AND stage = 'ended'";
            $stmt = $conn->query($sql);
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Przetwarzanie danych z tabeli games
            foreach ($games as $row) {
                $points_team1 = $row['points_team1'];
                $points_team2 = $row['points_team2'];
                $team1 = $row['team1'];
                $team2 = $row['team2'];
                // Aktualizacja danych dla team1
                $sql = "SELECT * FROM team_stats WHERE team_id = :team_id AND tournament_id = $id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':team_id' => $team1]);
                $existing_team1 = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing_team1) {
                    $sql = "UPDATE team_stats SET points = points + :points1 - :points2, ";
                    if ($points_team1 > $points_team2) {
                        $sql .= "win = win + 1";
                    } else {
                        $sql .= "lose = lose + 1";
                    }
                    $sql .= " WHERE team_id = :team_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':points1' => $points_team1, ':team_id' => $team1, ':points2' => $points_team2]);
                } else {
                    $sql = "INSERT INTO team_stats (team_id, points, win, lose, tournament_id) VALUES (:team_id, :points1, :win, :lose, $id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':team_id' => $team1, ':points1' => ($points_team1 - $points_team2), ':win' => ($points_team1 > $points_team2 ? 1 : 0), ':lose' => ($points_team1 > $points_team2 ? 0 : 1)]);
                }
                // Aktualizacja danych dla team2
                $sql = "SELECT * FROM team_stats WHERE team_id = :team_id AND tournament_id = $id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':team_id' => $team2]);
                $existing_team2 = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing_team2) {
                    $sql = "UPDATE team_stats SET points = points + :points2 - :points1, ";
                    if ($points_team2 > $points_team1) {
                        $sql .= "win = win + 1";
                    } else {
                        $sql .= "lose = lose + 1";
                    }
                    $sql .= " WHERE team_id = :team_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':points2' => $points_team2, ':team_id' => $team2, ':points1' => $points_team1]);
                } else {
                    $sql = "INSERT INTO team_stats (team_id, points, win, lose, tournament_id) VALUES (:team_id, :points2, :win, :lose, $id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':team_id' => $team2, ':points2' => ($points_team2 - $points_team1), ':win' => ($points_team2 > $points_team1 ? 1 : 0), ':lose' => ($points_team2 > $points_team1 ? 0 : 1)]);
                }
            }
} catch(PDOException $e) {
}

// Zamknięcie połączenia
$conn = null;
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT teams.id, team_stats.team_id, team_stats.win, team_stats.lose, team_stats.points, players.name FROM team_stats LEFT JOIN
    teams ON(
    team_stats.team_id = teams.id
    )
    LEFT JOIN players ON (
    teams.player1_id = players.id
    OR teams.player2_id = players.id
    )
    WHERE tournament_id = $id ORDER BY win DESC");
    $stmt->execute();
    $AllTeamStats = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $team_stats=$stmt->fetchAll();

}   catch(PDOException $e){    
}
$table = array();
foreach ($team_stats as $stats) {
    $table[$stats['id']][] = $stats;
}
    echo "<h1>Scoreboard</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Position</th><th>Team ID</th><th>Wins</th><th>Lost</th><th>Points</th></tr>";
    $position = 1;

    foreach($table as $team){
        echo "<tr>";
        echo "<td align=center>" . $position . "</td>";
        echo "<td align=center>" . $team[0]['name'] . ", " . $team[1]['name'] . "</td>";
        echo "<td align=center>" . $team[0]['win'] . "</td>";
        echo "<td align=center>" . $team[0]['lose'] . "</td>";
        echo "<td align=center>" . $team[0]['points'] . "</td>";
        echo "</tr>";
        $position++;
    }
    echo "</table>";
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "TRUNCATE TABLE team_stats";
    $conn->exec($sql);
} catch(PDOException $e) {
}