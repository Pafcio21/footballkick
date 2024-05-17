<form action="insert_mode.php" method="post">
<p>Name of tournament:</p>
<input type="text" name="tournament_name" id="a"><br>
<p>Choose players:</p>
<?php
include 'login_data.php';
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT name, id FROM players");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allPlayers=$stmt->fetchAll();
}   catch(PDOException $e){    
}

foreach($allPlayers as $player){
$a=$player['id'];
echo "<input type='checkbox' name='player[]' value='$a'>"
        .$player['name']."<br>";

}
$conn = null;
echo "<p>How many revanges:</p>";
echo "<input type=number name=revange value=2 min=1 max=24 width=100px>";


echo "<p>Choose game mode:</p>";
echo "<input type='radio' name='mode' disabled>
<label for='ledder'>ledder</label><br>";
echo "<input checked type='radio' name='mode'>
<label for='table'>table</label><br>";
echo "<input type='radio' name='mode' disabled>
<label for='ledder_with_loser'>ledder with losser</label><br>";
?>
<input type="submit" id="submit" value="Submit" >
</form>
