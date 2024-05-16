<?php include 'login_data.php';
$points_team1=$_POST['points_team1'];
$points_team2=$_POST['points_team2'];
$insert_id=$_POST['id'];
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT points_team1, points_team2, id, stage FROM games WHERE tournament_id = :tournament_id");
    $stmt->bindParam(':tournament_id', $insert_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $points=$stmt->fetchAll();
}   catch(PDOException $e){ 
  
}
$teamId=array_column($points, 'id');
$a=1;
$b="ended";
$c="waiting";
foreach($teamId as $id)
{
if($points_team1[$a] !=='' && $points_team2[$a] !=='')
{
$sql = "UPDATE games SET points_team1=$points_team1[$a], points_team2=$points_team2[$a], stage='$b'  WHERE id = $id";
$conn->exec($sql);
}else{
$sql = "UPDATE games SET stage='$c'  WHERE id = $id";
$conn->exec($sql);
}
$a=$a+1;
}
header("Location: games.php?id=$insert_id");
exit();