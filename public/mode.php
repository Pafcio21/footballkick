<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament creator</title>
    <script src="script.js" defer></script>
</head>
<body>
    <form action="insert_mode.php" method="post">
        <p>Name of tournament:</p>
        <input type="text" name="tournament_name" id="a" required><br>
        <p>Choose players:</p>
        <?php
        include 'login_data.php';
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT name, id FROM players");
            $stmt->execute();
            $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }

        foreach ($allPlayers as $player) {
            $id = htmlspecialchars($player['id']);
            $name = htmlspecialchars($player['name']);
            echo "<input type='checkbox' name='player[]' value='$id'> $name<br>";
        }

        echo "<p>How many revanges:</p>";
        echo "<input type='number' name='revange' value='2' min='1' max='24' style='width: 100px;'>";

        echo "<p>Choose game mode:</p>";
        echo "<input type='radio' name='mode' disabled>
        <label for='ledder'>ledder</label><br>";
        echo "<input checked type='radio' name='mode'>
        <label for='table'>table</label><br>";
        echo "<input type='radio' name='mode' disabled>
        <label for='ledder_with_loser'>ledder with loser</label><br>";
        
        $conn = null;
        ?>
        <input type="submit" id="submit" value="Submit" disabled>
    </form>
</body>
</html>




