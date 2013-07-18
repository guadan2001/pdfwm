<?php
/**
* 	PDF Water Mark class test
*
* 	By guadan2001
* 
* 	Latest Modification: 2013-7-17 9:52:34
*
*/

	require './pdfwm/pdfwm.class.php';
	
	$here = dirname(__FILE__);
	$out_dir = $here.'/docs/pdf';
	
//  	$pdfwm_docx = new pdfwm($here.'/docs/src/docx-test.docx', $out_dir, '');
//  	$pdfwm_docx->mark();
//  	unset($pdfwm_docx);
	
//   	$pdfwm_doc = new pdfwm($here.'/docs/src/doc-test.doc', $out_dir, '');
//   	$pdfwm_doc->mark();
//   	unset($pdfwm_doc);
	
//  	$pdfwm_txt = new pdfwm($here.'/docs/src/txt-test.txt', $out_dir, '');
//  	$pdfwm_txt->mark();
//  	unset($pdfwm_txt);
 	
//  	$pdfwm_jpg = new pdfwm($here.'/docs/src/jpg-test.jpg', $out_dir, '');
//  	$pdfwm_jpg->mark();
//  	unset($pdfwm_jpg);

	 	$pdfwm_long_jpg = new pdfwm($here.'/docs/src/long-jpg-test.jpg', $out_dir, '');
 		$pdfwm_long_jpg->mark();
 		unset($pdfwm_long_jpg);
 	
//  	$pdfwm_gif = new pdfwm($here.'/docs/src/gif-test.gif', $out_dir, '');
//  	$pdfwm_gif->mark();
//  	unset($pdfwm_gif);
 	
//  	$pdfwm_png = new pdfwm($here.'/docs/src/png-test.png', $out_dir, '');
//  	$pdfwm_png->mark();
//  	unset($pdfwm_png);
 	
//  	$pdfwm_psd = new pdfwm($here.'/docs/src/psd-test.psd', $out_dir, '');
//  	$pdfwm_psd->mark();
//  	unset($pdfwm_psd);
	
?>