<?php include 'login_data.php';
$name=$_POST['tournament_name'];
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO tournaments(`name`) Values('$name')";
    $conn->exec($sql);
    
}   catch(PDOException $e){    
}$tournament_id = $conn->lastInsertId();

$revange=$_POST['revange'];
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

    if (empty($matchedTeams)) {
        // nie ma jeszcze takiej druzyny
        
        $sql = "INSERT INTO teams(player1_id, player2_id) Values('$team[0]',$team[1])";
        $conn->exec($sql);
        $teamId = $conn->lastInsertId();
        $sql = "INSERT INTO tournament_teams(team_id, tournament_id) Values($teamId[0], $tournament_id)";
        $conn->exec($sql);

    } else {
        // jest taka druzyna
        $teamId = array_column($matchedTeams, 'id');
        $sql = "INSERT INTO tournament_teams(team_id, tournament_id) Values($teamId[0], $tournament_id)";
        $conn->exec($sql);
        /**
         * 1. Przypisanie id znalezionej druzyny do zmiennej $teamId
         */
    }


}
$conn = null;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT team_id FROM tournament_teams where tournament_id = :tournament_id");
    $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allTeams_id=$stmt->fetchAll();
}   catch(PDOException $e){    
}
class RoundRobinTournament {
    private $playingTeams;
    private $rounds;
    private $matches;

    public function __construct($playingTeams) {
        if(count($playingTeams)%2 != 0) {
            array_push($playingTeams, 'BYE');
        }
        $this->playingTeams = $playingTeams;
        $this->rounds = count($playingTeams) -1;
        $this->generateMatches();
    }
    private function generateMatches() {
        $numTeams = count($this->playingTeams);
        $half = $numTeams / 2;
        $playingTeams = $this->playingTeams;

        for($round = 0; $round<$this->rounds; $round++) {
            for($match = 0; $match < $half; $match++) {
                $team1 = $playingTeams[$match];
                $team2 = $playingTeams[$numTeams-1-$match]; 
                $this->matches[$round][$match] = [$team1, $team2];
            }
            array_splice($playingTeams, 1, 0, array_pop($playingTeams));
        }
    }
    public function getMatches() {
        return $this->matches;
    }
}
$playingTeams = array_column($allTeams_id, 'team_id');
$tournament = new RoundRobinTournament($playingTeams);
$matches = $tournament->getMatches();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT round, team1, team2, tournament_id FROM games");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allTeams=$stmt->fetchAll();
}   catch(PDOException $e){    
}
for($i = 1; $i <= $revange; $i++){
    foreach($matches as $round1 => $roundMatches1)
    {
        $round1=$round1 + 1; 
    
        foreach($roundMatches1 as $match1) {
        
            if($i % 2 == 0){
                if ($match1[0] !== 'BYE' && $match1[1] !== 'BYE') {
                    $sql = "INSERT INTO games(round, tournament_id, team1, team2) Values('$round1', '$tournament_id', '" . $match1[0] . "', '" . $match1[1] . "')";
                    $conn->exec($sql);
                }
            }else{
                if ($match1[0] !== 'BYE' && $match1[1] !== 'BYE') {
                    $sql = "INSERT INTO games(round, tournament_id, team1, team2) Values('$round1', '$tournament_id', '" . $match1[1] . "', '" . $match1[0] . "')";
                    $conn->exec($sql);    
                }
            }
        }
    }
}
header("Location: games.php?id=$tournament_id");
exit();