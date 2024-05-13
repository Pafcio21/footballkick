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

    if (empty($matchedTeams)) {
        // nie ma jeszcze takiej druzyny
        
        $sql = "INSERT INTO teams(player1_id, player2_id) Values('$team[0]',$team[1])";
        $conn->exec($sql);
        $teamId = $conn->lastInsertId();
        $sql = "INSERT INTO tournament_teams(team_id) Values($teamId[0])";
        $conn->exec($sql);

    } else {
        // jest taka druzyna
        $teamId = array_column($matchedTeams, 'id');
        $sql = "INSERT INTO tournament_teams(team_id) Values($teamId[0])";
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
    $stmt = $conn->prepare("SELECT team_id FROM tournament_teams");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allTeams=$stmt->fetchAll();
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
                $home = $playingTeams[$match];
                $away = $playingTeams[$numTeams-1-$match];
                $this->matches[$round][$match] = "$home vs $away";
            }
            array_splice($playingTeams, 1, 0, array_pop($playingTeams));
        }
    }
    public function getMatches() {
        return $this->matches;
    }
}
$playingTeams = array_column($allTeams, 'team_id');
$tournament = new RoundRobinTournament($playingTeams);
$matches = $tournament->getMatches();
foreach($matches as $round => $roundMatches)
{
    echo "Round" . ($round + 1) . ":\n <br>";
    foreach($roundMatches as $match) {
        echo $match . "\n <br>";
    }
    echo "\n";
}
