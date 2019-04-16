<?php
		$con = mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
		
		if (!$con){
			die ("Could not find database");	
		}
		
		mysql_select_db("seetoh88_mp08", $con);
		
		$result = mysql_query("SELECT a.User_ID, e.Event_Name, l.Street_Name, e.Host_Name, e.Time_Start, e.Time_End
		FROM Event AS e
		INNER JOIN Location AS l
		ON e.Location_ID = l.ID
		INNER JOIN Attendance AS a
		ON e.ID = a.Event_ID");
		

?>
<?php
include "fpdf.php";

$pdf = new FPDF();
$pdf->AddPage('L');

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,5,'Event List',0,1,'C');
$pdf->Ln(3);
$pdf->Cell(280,0.6,'','0','1','C',true);
$pdf->Ln(10);



$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,6,'User ID',0,0,'C');
$pdf->Cell(29,6,'Event Name',0,0,'C');
$pdf->Cell(49,6,'Location',0,0,'C');
$pdf->Cell(45,6,'Host Name',0,0,'C');
$pdf->Cell(45,6,'Time Start',0,0,'C');
$pdf->Cell(45,6,'Time End',0,0,'C');
$pdf->Ln(2);


	
//Connect to database
mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
mysql_select_db("seetoh88_mp08");



//First table: put all columns automatically

$sql = mysql_query('SELECT a.User_ID, e.Event_Name, l.Street_Name, e.Host_ID, e.Time_Start, e.Time_End
		FROM Event AS e
		INNER JOIN Location AS l
		ON e.Location_ID = l.ID
		INNER JOIN Attendance AS a
		ON e.ID = a.Event_ID');
while($data = mysql_fetch_array($sql)){

	$pdf->Ln(4);
	$pdf->SetFont('Arial','',7);

	$pdf->Cell(35,4,$data['User_ID'],1,0,'L');
	$pdf->Cell(37,4,$data['Event_Name'],1,0,'L');
	$pdf->Cell(35,4,$data['Street_Name'],1,0,'L');
	$pdf->Cell(35,4,$data['Host_Name'],1,0,'L');
	$pdf->Cell(40,4,$data['Time_Start'],1,0,'L');
	$pdf->Cell(35,4,$data['Time_End'],1,0,'R');
}
$pdf->Output();

?>