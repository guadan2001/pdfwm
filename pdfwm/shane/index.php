<?php
	require('fpdf.php');
	$pdf = new FPDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->Image('cat.png',10,10,100);
	$pdf->Output();
?>