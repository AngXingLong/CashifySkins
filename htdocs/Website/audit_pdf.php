<?php
		$con = mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
		
		if (!$con){
			die ("Could not find database");	
		}
		
		mysql_select_db("seetoh88_mp08", $con);
		
		$result = mysql_query("SELECT g.user_id, u.Username 
		FROM Login_Log AS g
		INNER JOIN UserProfile AS u
		ON g.user_id = u.ID");
		

?>
<?php
include "fpdf.php";

$pdf = new FPDF();
$pdf->AddPage('L');


$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,5,'Audit Trial',0,1,'C');
$pdf->Ln(3);
$pdf->Cell(280,0.6,'','0','1','C',true);
$pdf->Ln(10);




$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,6,'User ID',0,0,'C');
$pdf->Cell(29,6,'Username',0,0,'C');

$pdf->Ln(2);




//Connect to database
mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
mysql_select_db("seetoh88_mp08");


//First table: put all columns automatically

$sql = mysql_query("SELECT g.user_id, u.Username 
		FROM Login_Log AS g
		INNER JOIN UserProfile AS u
		ON g.user_id = u.ID");
while($data = mysql_fetch_array($sql)){

	$pdf->Ln(4);
	$pdf->SetFont('Arial','',7);

	$pdf->Cell(35,4,$data['user_id'],1,0,'L');
	$pdf->Cell(37,4,$data['Username'],1,0,'L');

}
$pdf->Output();

?>