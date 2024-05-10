<?php include 'login_data.php';

$players=$_POST['player'];

shuffle($players);


$player_count = count($players);
$players_per_team = 2;

$teams = array_chunk($players,$players_per_team);
$teamId_number=2;


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id, player1_id, player2_id FROM teams");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allTeams=$stmt->fetchAll();
}   catch(PDOException $e){    
}

foreach($teams as $team)
{
    // druzyny ktore juz istnieja
    $matchedTeams = array_filter($allTeams, function($t) use ($team) {
        return ($t['player1_id'] == $team[0] && $t['player2_id'] == $team[1]) || ($t['player1_id'] == $team[1] && $t['player2_id'] == $team[0]);
    });

    var_dump($matchedTeams);
    if (empty($matchedTeams)) {
        // nie ma jeszcze takiej druzyny
        
        $sql = "INSERT INTO teams(player1_id, player2_id) Values('$team[0]',$team[1])";
        $conn->exec($sql);
        $teamId = $conn->lastInsertId();
        var_dump($teamId);

    } else {
        // jest taka druzyna
        $teamId = array_column($matchedTeams, 'id');
        var_dump($teamId);

        /**
         * 1. Przypisanie id znalezionej druzyny do zmiennej $teamId
         */
    }


}
$conn = null;

