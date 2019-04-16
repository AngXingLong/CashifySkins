<?php
		$con = mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
		
		if (!$con){
			die ("Could not find database");	
		}
		
		mysql_select_db("seetoh88_mp08", $con);
		
		$result = mysql_query("SELECT u.ID, u.Name, u.NRIC, u.Username, u.StaffID, r.Role_Name, u.Inactive, u.Photo
		FROM UserProfile AS u
		INNER JOIN Role AS r
		ON u.Role = r.ID");
		

?>
<?php
include "fpdf.php";

$pdf = new FPDF();
$pdf->AddPage('L');

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,5,'Attendee List',0,1,'C');
$pdf->Ln(3);
$pdf->Cell(280,0.6,'','0','1','C',true);
$pdf->Ln(10);



$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,6,'User ID',0,0,'C');
$pdf->Cell(29,6,'Full Name',0,0,'C');
$pdf->Cell(49,6,'NRIC',0,0,'C');
$pdf->Cell(45,6,'Username',0,0,'C');
$pdf->Cell(35,6,'Staff ID',0,0,'C');
$pdf->Cell(25,6,'Role',0,0,'C');
$pdf->Cell(25,6,'Inactive',0,0,'C');
$pdf->Cell(25,6,'Photo',0,0,'C');
$pdf->Ln(2);


//Connect to database
mysql_connect("localhost","seetoh88_mp08", "0ruoba8");
mysql_select_db("seetoh88_mp08");



//First table: put all columns automatically

$sql = mysql_query("SELECT u.ID, u.Name, u.NRIC, u.Username, u.StaffID, r.Role_Name, u.Inactive, u.Photo
		FROM UserProfile AS u
		INNER JOIN Role AS r
		ON u.Role = r.ID");
while($data = mysql_fetch_array($sql)){

	$pdf->Ln(4);
	$pdf->SetFont('Arial','',7);

	$pdf->Cell(20,4,$data['ID'],1,0,'L');
	$pdf->Cell(37,4,$data['Name'],1,0,'L');
	$pdf->Cell(35,4,$data['NRIC'],1,0,'L');
	$pdf->Cell(35,4,$data['Username'],1,0,'L');
	$pdf->Cell(40,4,$data['StaffID'],1,0,'L');
	$pdf->Cell(20,4,$data['Role_Name'],1,0,'L');
	$pdf->Cell(20,4,$data['Inactive'],1,0,'L');
	$pdf->Cell(35,4,$data['Photo'],1,0,'R');

}
$pdf->Output();




?>