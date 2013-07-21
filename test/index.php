<?php
	require('fpdf.php');
	$pdf = new FPDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->Image('gif-test.gif',0,0,210,297);
	$pdf->Output();
?>