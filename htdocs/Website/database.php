<?
header("Access-Control-Allow-Origin: *");
global $conn;

$hostname = "localhost";
$username = "seetoh88_mp08";
$dbname = "seetoh88_mp08";
$password = "0ruoba8";
$charset = "utf8";

mysql_connect($hostname,$username,$password);
mysql_select_db($dbname) or die("Could not find database");

$conn = new PDO("mysql:host=$servername;dbname=".$dbname.";charset=".$charset."", $username, $password);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function select($query,$array = null){
	
		global $conn;
		
		$stmt = $conn->prepare($query);
		$stmt->execute($array);		
		$stmt->setFetchMode(PDO::FETCH_ASSOC); 
		
		return $stmt->fetchAll();
		
}

?>