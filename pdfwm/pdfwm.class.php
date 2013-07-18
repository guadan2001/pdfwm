<?php
/*
 * 	PDF Water Mark class v0.1
 * 
 * 	By guadan2001 & Shane_Wayne
 * 
 * 	Latest Modification: 2013-7-17 9:52:34
 * 
 * 
 * 	TODO:
 * 	-----------------------------------------
 * 	* mark()主流程实现，含进程控制 - sx
 *  * docx转pdf实现 - gd
 *  * jpg, png, gif转pdf实现 - gd
 *  * 水印参数配置接口实现  - sx * 
 * 
 * 	BUGS:
 *  ----------------------------------------- 
 * 
 */

define('PDFWM_ROOT', dirname(__FILE__));

require(PDFWM_ROOT.'/libs/fpdf/fpdf.php');
require(PDFWM_ROOT.'/libs/php-psd/PSDReader.php');

class pdfwm {
	
	var $_src_file;
	var $_out_dir;
	var $_wm_file;
	
	var $_wm_position;
	var $_wm_rotation;
	var $_wm_alaph;
	
	public function __construct($src_file, $out_dir, $wm_file)
	{
		$this->_src_file = $src_file;
		$this->_out_dir = $out_dir;
		$this->_wm_file = $wm_file;

		$this->_wm_position = 9;
		$this->_wm_rotation = 0;
		$this->_wm_alaph = 100;
	}
	
	public function mark()
	{
		$this->convert_to_pdf();
	}
	
	/**
	 * sets water mark's position: 1-9
	 *
	 * @param int $wm_position
	 * @return null
	 */
	public function set_wm_position($wm_position = 9)
	{
		$this->_wm_position = $wm_position;
	}
	
	/**
	 * sets water mark's rotation angle: 0-360
	 *
	 * @param int $wm_rotation
	 * @return null
	 */
	public function set_wm_rotation($wm_rotation = 0)
	{
		$wm_rotation = $wm_rotation % 360;
		$this->_wm_rotation = $wm_rotation;
	}
	
	/**
	 * sets water mark's rotation angle: 0-360
	 *
	 * @param int $wm_rotation
	 * @return null
	 */
	public function set_wm_alaph($wm_alaph = 100)
	{
		
	}
	
	/**
	 * converts source file to PDF format
	 *
	 * @param null
	 * @return null
	 */
	private function convert_to_pdf()
	{
		if(!file_exists($this->_src_file))
		{
			throw new Exception('source file is not existed. $src_file='.$this->_src_file);
			return;
		}
		
		$in_path = pathinfo($this->_src_file);
		$in_filename_prefix = substr($in_path['basename'], 0, strripos($in_path['basename'], '.'));
		$out_file = $this->_out_dir.'/'.$in_filename_prefix.'.pdf';
		
		$in_path['extension'] = strtolower($in_path['extension']);
		
		switch($in_path['extension'])
		{
			case 'doc':
			case 'docx':
			case 'txt':
				if(file_exists($out_file))
				{
					throw new Exception('PDF file is existed, conversion failed. $pdf_file='.$this->_pdf_file);
					return;
				}
				$soffice = '"D:\Program Files\LibreOffice 4.0\program\soffice.exe"';
				$cmd = $soffice." --headless -convert-to pdf -outdir ".$this->_out_dir." ".$this->_src_file;
				sleep(3);
				exec($cmd);
				echo $cmd.'<br>';
				break;
			case 'jpg':
				echo "jpg".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				break;
			case 'gif':
				echo "gif".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				break;
			case 'png':
				echo "png".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
			case 'psd':
				$tmp_file = PDFWM_ROOT.'/'.rand(0, 1000).'.jpg';
				imagejpeg(imagecreatefrompsd($this->_src_file), $tmp_file, 100);
				sleep(2);
				if(!file_exists($tmp_file))
				{
					throw new Exception('PSD conversion failed.');
					return;
				}
				else
				{
					$pdf = new FPDF('P','mm','A4');
					$pdf->AddPage();
					$pdf->Image($tmp_file,10,10,100);
					$pdf->Output($out_file,'F');
					unlink($tmp_file);
					unset($pdf);
				}
				break;
		}
	}
}
	
	
?>