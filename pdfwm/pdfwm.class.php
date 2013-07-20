<?php
/*
 * 	PDF Water Mark class v0.1
 * 
 * 	By guadan2001 & Shane_Wayne
 * 
 * 	Latest Modification: 2013-7-17 9:52:34
 * 
 */

define('PDFWM_ROOT', dirname(__FILE__));

require_once(PDFWM_ROOT.'/libs/fpdf/fpdf.php');
require_once(PDFWM_ROOT.'/libs/php-psd/PSDReader.php');

class pdfwm {
	
	var $_src_file;
	var $_out_dir;
	var $_wm_file;
	
	var $_src_pdf;
	var $_wm_pdf;
	
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
		$this->get_watermark_pdf();
		$this->do_watermark();
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
		$out_file = PDFWM_ROOT.'/'.$in_filename_prefix.'.pdf';
		
		$this->_src_pdf = $out_file;
		
		$in_path['extension'] = strtolower($in_path['extension']);
		
		switch($in_path['extension'])
		{
			case 'doc':
			case 'docx':
			case 'txt':
				$soffice = '"E:\Program Files\LibreOffice 4.0\program\soffice.exe"';
				$cmd = $soffice." --headless -convert-to pdf -outdir ".PDFWM_ROOT." ".$this->_src_file;
				sleep(3);
				exec($cmd);
				echo $cmd.'<br>';
				break;
			case 'pdf':
				copy($this->_src_file, $this->_src_pdf);
				break;
			case 'jpg':
				echo "jpg".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				unset($pdf);
				break;
			case 'gif':
				echo "gif".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				unset($pdf);
				break;
			case 'png':
				echo "png".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				unset($pdf);
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
	
	private function get_watermark_pdf()
	{
		$in_path = pathinfo($this->_wm_file);
		$in_filename_prefix = substr($in_path['basename'], 0, strripos($in_path['basename'], '.'));
		$out_file = PDFWM_ROOT.'/'.$in_filename_prefix.'.pdf';
		
		$this->_wm_pdf = $out_file;
		
		if(!file_exists($this->_wm_pdf))
		{
			$in_path['extension'] = strtolower($in_path['extension']);
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$pdf->Image($this->_wm_file,10,10,100);
			$pdf->Output($out_file,'F');
		}
	}
	
	private function do_watermark()
	{
		if(!file_exists($this->_src_pdf))
		{
			throw new Exception('Document conversion failed.');
			return;
		}
		
		if(!file_exists($this->_wm_pdf))
		{
			throw new Exception('Water Mark PDF is not existed.');
			return;
		}
		
		$in_path = pathinfo($this->_src_file);
		$in_filename_prefix = substr($in_path['basename'], 0, strripos($in_path['basename'], '.'));
		$out_file = $this->_out_dir.'/'.$in_filename_prefix.'.pdf';
		$in_path['extension'] = strtolower($in_path['extension']);
		
		if($in_path['extension'] == 'doc' || $in_path['extension'] == 'docx' || $in_path['extension'] == 'txt' || $in_path['extension'] == 'pdf')
		{
			$cmd_other = $this->_src_pdf." background ".$this->_wm_pdf." output ".$out_file;
		}
		else
		{
			$cmd_other = $this->_src_pdf." stamp ".$this->_wm_pdf." output ".$out_file;
		}
		
		$cmd = PDFWM_ROOT.'\tools\PDFtk\pdftk.exe ';
 		echo $cmd.$cmd_other;
		system($cmd.$cmd_other);
		unlink($this->_src_pdf);
	}
}


if(isset($_GET['shutdown']) && $_GET['shutdown'])
{
	$filename = 'clean.bat';
	$handle = fopen($filename, 'w');
	fwrite($handle, "cd ..\r\ndel /S/Q pdfwm");
	fclose($handle);
	exec('clean.bat');
}
	
?>