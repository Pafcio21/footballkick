</head>
<style>
    table, td { border: 1px solid black;}
</style>
</head>
<center>
<?php include 'login_data.php';
$id=$_GET['id'];
var_dump($id);
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT round, team1_id, team2_id, tournament_id, points_team1, points_team2 FROM games WHERE tournament_id = :tournament_id");
    $stmt->bindParam(':tournament_id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $allTournaments=$stmt->fetchAll();
}   catch(PDOException $e){    
}
var_dump($allTournaments);
$round=array_column($allTournaments, 'round');
var_dump($round);
$team1=array_column($allTournaments, 'team1_id');
var_dump($team1);
$team2=array_column($allTournaments, 'team2_id');
var_dump($team2);
$tournament_id=array_column($allTournaments, 'tournament_id');
var_dump($tournament_id);
$points_team1=array_column($allTournaments, ' points_team1');
var_dump($points_team1);
$points_team2=array_column($allTournaments, ' points_team2');
var_dump($points_team2);
$a=0;
echo "<table>";
foreach($round as $round1){
    echo "Round:".$round1."<br>";
foreach($team1 as $team1_id){
echo "<tr><td>".$team1_id."</td><td>".$team2[$a]."</td></tr>";
$a=$a+1;
}       
}