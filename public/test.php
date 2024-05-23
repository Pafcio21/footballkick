<?php
include 'login_data.php';

$points_team1 = $_POST['points_team1'];
$points_team2 = $_POST['points_team2'];
$insert_id = $_POST['id'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pobieranie informacji o grach
    $stmt = $conn->prepare("SELECT points_team1, points_team2, id, team1, team2, stage FROM games WHERE tournament_id = :tournament_id AND stage <> 'ended'");
    $stmt->bindParam(':tournament_id', $insert_id, PDO::PARAM_STR);
    $stmt->execute();
    $points = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Przygotowanie do aktualizacji
    $teamId = array_column($points, 'id');
    $a = 0;
    $b = "ended";
    $c = "waiting";

    foreach ($teamId as $id) {
        if ($points_team1[$a] !== '' && $points_team2[$a] !== '') {
            // Pobieranie ID drużyn
            $team1_id = $points[$a]['team1'];
            $team2_id = $points[$a]['team2'];

            // Pobieranie obecnych wartości ELO dla drużyn
            $stmt = $conn->prepare("SELECT id, elo FROM tournament_teams WHERE id IN (:team1_id, :team2_id)");
            $stmt->bindParam(':team1_id', $team1_id, PDO::PARAM_INT);
            $stmt->bindParam(':team2_id', $team2_id, PDO::PARAM_INT);
            $stmt->execute();
            $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Przypisanie wartości ELO
            $elo_team1 = 0;
            $elo_team2 = 0;
            foreach ($teams as $team) {
                if ($team['id'] == $team1_id) {
                    $elo_team1 = $team['elo'];
                } else {
                    $elo_team2 = $team['elo'];
                }
            }

            // Obliczanie nowych wartości ELO
            list($new_elo_team1, $new_elo_team2) = calculateElo($elo_team1, $elo_team2, $points_team1[$a], $points_team2[$a]);

            // Aktualizacja wartości ELO w bazie danych
            $stmt = $conn->prepare("UPDATE tournament_teams SET elo = :elo WHERE id = :team_id");
            $stmt->bindParam(':elo', $new_elo_team1, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $team1_id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE tournament_teams SET Elo = :elo WHERE id = :team_id");
            $stmt->bindParam(':elo', $new_elo_team2, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $team2_id, PDO::PARAM_INT);
            $stmt->execute();

            // Aktualizacja punktów i stanu gry
            $sql = "UPDATE games SET points_team1 = :points_team1, points_team2 = :points_team2, stage = :stage WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':points_team1', $points_team1[$a], PDO::PARAM_INT);
            $stmt->bindParam(':points_team2', $points_team2[$a], PDO::PARAM_INT);
            $stmt->bindParam(':stage', $b, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = "UPDATE games SET stage = :stage WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':stage', $c, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        $a++;
    }
    header("Location: games.php?id=$insert_id");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function calculateElo($elo1, $elo2, $score1, $score2, $k = 32) {
    // Obliczanie oczekiwanych wyników
    $expected1 = 1 / (1 + pow(10, ($elo2 - $elo1) / 400));
    $expected2 = 1 - $expected1;

    // Ustalenie rzeczywistych wyników meczu
    if ($score1 > $score2) {
        $result1 = 1;
        $result2 = 0;
    } elseif ($score1 < $score2) {
        $result1 = 0;
        $result2 = 1;
    } else {
        $result1 = 0.5;
        $result2 = 0.5;
    }

    // Obliczanie nowych wartości ELO
    $new_elo1 = $elo1 + $k * ($result1 - $expected1);
    $new_elo2 = $elo2 + $k * ($result2 - $expected2);

    return [round($new_elo1), round($new_elo2)];
}
?>