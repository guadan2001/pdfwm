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

require_once(PDFWM_ROOT.'/libs/PHPImageWorkshop/Exception/ImageWorkshopBaseException.php');
require_once(PDFWM_ROOT.'/libs/PHPImageWorkshop/Exception/ImageWorkshopException.php');
require_once(PDFWM_ROOT.'/libs/PHPImageWorkshop/Core/ImageWorkshopLib.php');
require_once(PDFWM_ROOT.'/libs/PHPImageWorkshop/Core/ImageWorkshopLayer.php');
require_once(PDFWM_ROOT.'/libs/PHPImageWorkshop/ImageWorkshop.php');

use PHPImageWorkshop\ImageWorkshop;

class pdfwm {
	
	var $_src_file;
	var $_out_dir;
	var $_wm_file;
	
	var $_src_pdf;
	var $_wm_pdf;
	
	var $_wm_position;
	var $_wm_rotation;
	var $_wm_alaph;
	var $_wm_mode;
	
	public static $WM_MODE_NORMAL = 0;
	public static $WM_MODE_TILE = 1;	
	
	public function __construct($src_file, $out_dir, $wm_file)
	{
		$this->_src_file = $src_file;
		$this->_out_dir = $out_dir;
		$this->_wm_file = $wm_file;

		$this->_wm_mode = pdfwm::$WM_MODE_NORMAL;
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
	
	public function pdfcat($Inpdf1,$Inpdf2)
	{
		$in_path = pathinfo($Inpdf1);
		$in_filename_prefix = substr($in_path['basename'], 0, strripos($in_path['basename'], '.'));
		$out_file = $this->_out_dir.'/cat_'.$in_filename_prefix.'.pdf';
		$in_path['extension'] = strtolower($in_path['extension']);
		
		$cmd_other = $Inpdf1." ".$Inpdf2." cat output ".$out_file;
		
		$cmd = PDFWM_ROOT.'\tools\PDFtk\pdftk.exe ';
 		//echo $cmd.$cmd_other;
		system($cmd.$cmd_other);
		
		return $out_file;
	}
	
	public function set_wm_mode($mode = pdfwm::WM_MODE_NORMAL)
	{
		$this->_wm_mode = $mode;
	}
	
	/**
	 * sets water mark's position: 1-9
	 *
	 * @param int $wm_position
	 * @return null
	 */
	public function set_wm_position($wm_position = 9)
	{
		$this->_wm_position = $wm_position - 1;
	}
	
	/**
	 * sets water mark's rotation angle: 0-360
	 *
	 * @param int $wm_rotation
	 * @return null
	 */
	public function set_wm_rotation($wm_rotation = 0)
	{
		$in_path = pathinfo($this->_wm_file);
		$in_filename_prefix = substr($in_path['basename'], 0, strripos($in_path['basename'], '.'));
		$out_file = PDFWM_ROOT.'/'.$in_filename_prefix.'-Rotated'.'.png';
		
		$wm_info = getimagesize($this->_wm_file);

		switch($wm_info[2])
		{
			case 1:
			$source = imagecreatefromgif($this->_wm_file);
			break;
			case 2:
			$source = imagecreatefromjpeg($this->_wm_file);
			break;
			case 3:
			$source = imagecreatefrompng($this->_wm_file);
			break;
			default:
			die("Unsupported Image Format");
		}

		$rotate = imagerotate($source, $degrees,  imageColorAllocateAlpha($source, 255, 255, 255, 127));
		imagealphablending($rotate, false);
		imagesavealpha($rotate, true);
		imagepng($rotate,$out_file);
		$this->_wm_file = $out_file;
		imagedestroy($source);
		imagedestroy($rotate);
	}
	
	/**
	 * sets water mark's rotation angle: 0-360
	 
	 * @param int $wm_rotation
	 * @return null
	 */
	public function set_wm_alaph($wm_alaph = 100)
	{
		$in_path = pathinfo($this->_wm_file);
		$in_filename_prefix = substr($in_path['basename'], 0, strripos($in_path['basename'], '.'));
		$out_file = $in_filename_prefix.'-Alaphed'.'.png';

		$TestLayer = ImageWorkShop::initFromPath($this->_wm_file);
		echo $TestLayer->opacity($wm_alaph);
		$TestLayer->save(PDFWM_ROOT.'/',$out_file);
		$this->_wm_file = PDFWM_ROOT.'/'.$out_file;
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
				$soffice = '"D:\Program Files\LibreOffice 4.0\program\soffice.exe"';
				$cmd = $soffice." --headless -convert-to pdf -outdir ".PDFWM_ROOT." ".$this->_src_file;
				sleep(3);
				exec($cmd);
				//echo $cmd.'<br>';
				break;
			case 'pdf':
				copy($this->_src_file, $this->_src_pdf);
				break;
			case 'jpg':
				//echo "jpg".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				unset($pdf);
				break;
			case 'gif':
				//echo "gif".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				unset($pdf);
				break;
			case 'png':
				//echo "png".$this->_src_file;
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->Image($this->_src_file,10,10,100);
				$pdf->Output($out_file,'F');
				unset($pdf);
				break;
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
		
		if(file_exists($out_file))
		{
			unlink($out_file);
		}
		
		if($this->_wm_mode == pdfwm::$WM_MODE_NORMAL)
		{
			$x = 35 + 70 * ($this->_wm_position % 3);
			$y = 49.5 + 99 * (intval($this->_wm_position / 3));
			
			list($PicWidth,$PicHeight) = getimagesize($this->_wm_file);
			
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$pdf->Image($this->_wm_file,$x-$PicWidth*25.4/192,$y-$PicHeight*25.4/192);
			$pdf->Output($out_file,'F');
			
			unset($pdf);
		}
		else if($this->_wm_mode == pdfwm::$WM_MODE_TILE)
		{
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			list($PicWidth,$PicHeight) = getimagesize($this->_wm_file);
			$NumImageX =intval(210 / ($PicWidth * 25.4 / 96)) + 1;
			$NumImageY =intval(297 / ($PicHeight * 25.4 / 96)) + 1;
			for($i = 0 ; $i < $NumImageX ; $i++)
			{
				for($j = 0 ; $j < $NumImageY ; $j++)
				{
					$pdf->Image($this->_wm_file,$i * $PicWidth * 25.4 / 96,$j * $PicHeight * 25.4 / 96);
				}
			}
			//$pdf->Image($this->_wm_file,0,0,210,297);
			$pdf->Output($out_file,'F');
			
			unset($pdf);
		}
		
		if(file_exists($out_file))
		{
			$this->_wm_pdf = $out_file;
		}
		else
		{
			throw new Exception('Water Mark conversion failed.');
			return;
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
		
		if(file_exists($out_file))
		{
			unlink($out_file);
		}
		
		$cmd = PDFWM_ROOT.'\tools\PDFtk\pdftk.exe ';
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