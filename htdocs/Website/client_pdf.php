<?php
		$con = mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
		
		if (!$con){
			die ("Could not find database");	
		}
		
		mysql_select_db("seetoh88_mp08", $con);
		
		$result = mysql_query("SELECT Name,Contact,Address
		FROM Organisation");
		

?>
<?php
include "fpdf.php";


$pdf = new FPDF();
$pdf->AddPage('L');

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,5,'Client List',0,1,'C');
$pdf->Ln(3);
$pdf->Cell(280,0.6,'','0','1','C',true);
$pdf->Ln(10);



$pdf->SetFont('Arial','B',10);

$pdf->Cell(49,6,'Name',0,0,'C');
$pdf->Cell(45,6,'Contact',0,0,'C');
$pdf->Cell(45,6,'Address',0,0,'C');
$pdf->Ln(2);
	


//Connect to database
mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
mysql_select_db("seetoh88_mp08");



//First table: put all columns automatically
$sql = mysql_query('SELECT Name,Contact,Address
		FROM Organisation');
while($data = mysql_fetch_array($sql)){

	$pdf->Ln(4);
	$pdf->SetFont('Arial','',10);

	$pdf->Cell(35,4,$data['Name'],1,0,'L');
	$pdf->Cell(32,4,$data['Contact'],1,0,'L');
	$pdf->Cell(75,4,$data['Address'],1,0,'R');

}
$pdf->Output();
?>