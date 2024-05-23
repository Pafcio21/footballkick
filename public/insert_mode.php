<?php
include 'login_data.php';

$name = $_POST['tournament_name'];
$revange = $_POST['revange'];
$players = $_POST['player'];
shuffle($players);
$player_count = count($players);
$players_per_team = 2;
$teams = array_chunk($players, $players_per_team);

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insert new tournament
    $stmt = $conn->prepare("INSERT INTO tournaments(`name`) VALUES (:name)");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $tournament_id = $conn->lastInsertId();
    
    // Fetch existing teams
    $stmt = $conn->prepare("SELECT id, player1_id, player2_id FROM teams");
    $stmt->execute();
    $allTeams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($teams as $team) {
        $matchedTeams = array_filter($allTeams, function($t) use ($team) {
            return ($t['player1_id'] == $team[0] && $t['player2_id'] == $team[1]) || ($t['player1_id'] == $team[1] && $t['player2_id'] == $team[0]);
        });

        if (empty($matchedTeams)) {
            // Insert new team
            $stmt = $conn->prepare("INSERT INTO teams(player1_id, player2_id) VALUES (:player1, :player2)");
            $stmt->bindParam(':player1', $team[0], PDO::PARAM_INT);
            $stmt->bindParam(':player2', $team[1], PDO::PARAM_INT);
            $stmt->execute();
            $teamId = $conn->lastInsertId();
        } else {
            // Use existing team ID
            foreach($matchedTeams as $matchedTeams){
            $teamId = $matchedTeams['id'];
            }
        }

        // Insert team into tournament
        $stmt = $conn->prepare("INSERT INTO tournament_teams(team_id, tournament_id) VALUES (:team_id, :tournament_id)");
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Fetch all team IDs for the tournament
    $stmt = $conn->prepare("SELECT team_id FROM tournament_teams WHERE tournament_id = :tournament_id");
    $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
    $stmt->execute();
    $allTeams_id = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate matches
    class RoundRobinTournament {
        private $playingTeams;
        private $rounds;
        private $matches;

        public function __construct($playingTeams) {
            if(count($playingTeams) % 2 != 0) {
                array_push($playingTeams, 'BYE');
            }
            $this->playingTeams = $playingTeams;
            $this->rounds = count($playingTeams) - 1;
            $this->generateMatches();
        }

        private function generateMatches() {
            $numTeams = count($this->playingTeams);
            $half = $numTeams / 2;
            $playingTeams = $this->playingTeams;

            for($round = 0; $round < $this->rounds; $round++) {
                for($match = 0; $match < $half; $match++) {
                    $team1 = $playingTeams[$match];
                    $team2 = $playingTeams[$numTeams - 1 - $match];
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

    // Insert matches
    for($i = 1; $i <= $revange; $i++) {
        foreach($matches as $round1 => $roundMatches1) {
            $round1 = $round1 + 1;
            foreach($roundMatches1 as $match1) {
                if ($match1[0] !== 'BYE' && $match1[1] !== 'BYE') {
                    $team1 = ($i % 2 == 0) ? $match1[0] : $match1[1];
                    $team2 = ($i % 2 == 0) ? $match1[1] : $match1[0];
                    $stmt = $conn->prepare("INSERT INTO games(round, tournament_id, team1, team2) VALUES (:round, :tournament_id, :team1, :team2)");
                    $stmt->bindParam(':round', $round1, PDO::PARAM_INT);
                    $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
                    $stmt->bindParam(':team1', $team1, PDO::PARAM_INT);
                    $stmt->bindParam(':team2', $team2, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
    }

    header("Location: games.php?id=$tournament_id");
    exit();

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>