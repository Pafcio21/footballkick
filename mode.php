<p>Choose players:</p>
<form action="insert_mode.php" method="post">
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
?>

<p>Choose game mode:</p>
<input type="radio" id="ledder" name="mode" value="ledder">
<label for="ledder">ledder</label><br>
<input type="radio" id="table" name="mode" value="table">
<label for="table">table</label><br>
<input type="radio" id="ledder_with_l" name="mode" value="ledder_with_l">
<label for="ledder_with_l">ledder with losser</label><br><br>
<input type="submit" id="submit" value="Submit" >
</form>
