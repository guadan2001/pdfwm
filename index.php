<?php
/**
* 	PDF Water Mark class test
*
* 	By guadan2001
* 
* 	Latest Modification: 2013-7-17 9:52:34
*
*
* 	TODO:
* 	-----------------------------------------
* 	*
*
*
*
* 	BUGS:
*  -----------------------------------------
*
*/

	require './pdfwm/pdfwm.class.php';
	
	$here = dirname(__FILE__);
	
 	$pdfwm_docx = new pdfwm($here.'/pdfwm/docs/src/docx-test.docx', $here.'/pdfwm/docs/pdf/1.pdf', '');
 	$pdfwm_docx->mark();
	
//  	$pdfwm_doc = new pdfwm($here.'/pdfwm/docs/src/test.doc', $here.'/pdfwm/docs/pdf/2.pdf', '');
//  	$pdfwm_doc->mark();
	
// 	$pdfwm_txt = new pdfwm($here.'/pdfwm/docs/src/readme.txt', $here.'/pdfwm/docs/pdf/3.pdf', '');
// 	$pdfwm_txt->mark();
	
?>
