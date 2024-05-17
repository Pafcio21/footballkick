<?php  include 'login_data.php';
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