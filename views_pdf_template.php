<?php

/**
 * A direct include is not realy possible. The basic functions of drupal must be 
 * present.
 * 
 */

require_once variable_get('views_pdf_tcpdf_path', 'lib/tcpdf/tcpdf.php');
require_once variable_get('views_pdf_fpdi_bridge_path', 'lib/fpdi/fpdi2tcpdf_bridge.php');
require_once variable_get('views_pdf_fpdi_path', 'lib/fpdi/fpdi.php');



class PdfTemplate extends FPDI
{
	protected static $fontList = NULL;
	protected static $fontListClean = NULL;
	protected static $templateList = NULL;
	protected $defaultFontStyle = '';
	protected $defaultFontFamily = 'helvetica';
	protected $defaultFontSize = '11';
	protected $defaultTextAlign = 'L';
	protected $defaultFontColor = '000000';
	protected $defaultPageTemplateFiles = array();
	protected $mainContentPageNumber = 0;
	protected $rowContentPageNumber = 0;
	protected $defaultOrientation = 'P';
	protected $defaultFormat = 'A4';
	protected $addNewPageBeforeNextContent = false;
	protected $elements = array();
	protected $headerFooterData = array();
	protected $view = NULL;
	protected $headerFooterOptions = array();
	
	
	
	protected static $defaultFontList = array(
		'almohanad' => 'AlMohanad',
		'arialunicid0' => 'ArialUnicodeMS',
		'courier' => 'Courier',
		'courierb' => 'Courier Bold',
		'courierbi' => 'Courier Bold Italic',
		'courieri' => 'Courier Italic',
		'dejavusans' => 'DejaVuSans',
		'dejavusansb' => 'DejaVuSans-Bold',
		'dejavusansbi' => 'DejaVuSans-BoldOblique',
		'dejavusansi' => 'DejaVuSans-Oblique',
		'dejavusanscondensed' => 'DejaVuSansCondensed',
		'dejavusanscondensedb' => 'DejaVuSansCondensed-Bold',
		'dejavusanscondensedbi' => 'DejaVuSansCondensed-BoldOblique',
		'dejavusanscondensedi' => 'DejaVuSansCondensed-Oblique',
		'dejavusansmono' => 'DejaVuSansMono',
		'dejavusansmonob' => 'DejaVuSansMono-Bold',
		'dejavusansmonobi' => 'DejaVuSansMono-BoldOblique',
		'dejavusansmonoi' => 'DejaVuSansMono-Oblique',
		'dejavuserif' => 'DejaVuSerif',
		'dejavuserifb' => 'DejaVuSerif-Bold',
		'dejavuserifbi' => 'DejaVuSerif-BoldItalic',
		'dejavuserifi' => 'DejaVuSerif-Italic',
		'dejavuserifcondensed' => 'DejaVuSerifCondensed',
		'dejavuserifcondensedb' => 'DejaVuSerifCondensed-Bold',
		'dejavuserifcondensedbi' => 'DejaVuSerifCondensed-BoldItalic',
		'dejavuserifcondensedi' => 'DejaVuSerifCondensed-Italic',
		'freemono' => 'FreeMono',
		'freemonob' => 'FreeMonoBold',
		'freemonobi' => 'FreeMonoBoldOblique',
		'freemonoi' => 'FreeMonoOblique',
		'freesans' => 'FreeSans',
		'freesansb' => 'FreeSansBold',
		'freesansbi' => 'FreeSansBoldOblique',
		'freesansi' => 'FreeSansOblique',
		'freeserif' => 'FreeSerif',
		'freeserifb' => 'FreeSerifBold',
		'freeserifbi' => 'FreeSerifBoldItalic',
		'freeserifi' => 'FreeSerifItalic',
		'hysmyeongjostdmedium' => 'HYSMyeongJoStd-Medium-Acro',
		'helvetica' => 'Helvetica',
		'helveticab' => 'Helvetica Bold',
		'helveticabi' => 'Helvetica Bold Italic',
		'helveticai' => 'Helvetica Italic',
		'kozgopromedium' => 'KozGoPro-Medium-Acro',
		'kozminproregular' => 'KozMinPro-Regular-Acro',
		'msungstdlight' => 'MSungStd-Light-Acro',
		'stsongstdlight' => 'STSongStd-Light-Acro',
		'symbol' => 'Symbol',
		'times' => 'Times New Roman',
		'timesb' => 'Times New Roman Bold',
		'timesbi' => 'Times New Roman Bold Italic',
		'timesi' => 'Times New Roman Italic',
		'zapfdingbats' => 'Zapf Dingbats',
		'zarbold' => 'ZarBold'
	);
	
	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
		$this->defaultOrientation = $orientation;
		$this->defaultFormat = $format;
	}
	
	public function setDefaultFontSize($size) {
		$this->defaultFontSize = $size;
	}
	
	public function setDefaultFontFamily($family) {
		$this->defaultFontFamily = $family;
	}
	
	public function setDefaultFontStyle($style) {
		$this->defaultFontStyle = $style;
	}
	
	public function setDefaultTextAlign($style) {
		$this->defaultTextAlign = $style;
	}
	
	public function setDefaultFontColor($color) {
		$this->defaultFontColor = $color;
	}
	
	public function setDefaultPageTemplate($path, $key, $pageNumbering = 'main') {
		$this->defaultPageTemplateFiles[$key] = array(
			'path' => $path,
			'numbering' => $pageNumbering
		);
	}
	
	function Header() {
	
	}
	
	function Footer() {
	
	}
	
	public function convertHexColorToArray($hex) {
		if(strlen($hex) == 6) {
			$r = substr($hex, 0, 2);
			$g = substr($hex, 2, 2);
			$b = substr($hex, 4, 2);
			return array(hexdec($r), hexdec($g), hexdec($b));
		
		}
		elseif(strlen($hex) == 3) {
			$r = substr($hex, 0, 1);
			$g = substr($hex, 1, 1);
			$b = substr($hex, 2, 1);
			return array(hexdec($r), hexdec($g), hexdec($b));
		
		}
		else {
			return array();
		}
		
	}
	
	public function setHeaderFooter($row, $options, $view) {
		if($this->getPage() > 0 && !isset($this->headerFooterData[$this->getPage()])) {
			$this->headerFooterData[$this->getPage()] = & $row;
		}
		$this->headerFooterOptions = $options;
		$this->view =& $view;
		
	}
	
	public function Close() {
		// Print the Header & Footer 
		$row = array();
		for($page = 1; $page <= $this->getNumPages(); $page++) {
			$this->setPage($page);
			
			if(isset($this->headerFooterData[$page])) {
				$row = $this->headerFooterData[$page];
			}
			
			if(is_array($this->headerFooterOptions['formats']))
			{
				foreach($this->headerFooterOptions['formats'] as $id => $options) {
				
					if($options['position']['object'] == 'header_footer') {
						$fieldOptions = $options;
						$fieldOptions['position']['object'] = 'page';
						$this->InFooter = true;
					
						// backup margins
						$ml = $this->lMargin;
						$mr = $this->rMargin;
						$mt = $this->tMargin;
						$this->SetMargins(0,0,0);
					
						$this->drawContent($row, $fieldOptions, $this->view, $id);
						$this->InFooter = false;
					
						// restore margins
						$this->SetMargins($ml,$mt,$mr);
					}
				}
			}
			
			
		}
		
		// call parent:
		parent::Close();	
		
	}
	
	public function drawContent($row, $options, &$view = NULL, $key = NULL) {
		
		// Check if there is a page, if not add it:
		if($this->getPage() == 0 or $this->addNewPageBeforeNextContent == true) {
			$this->addNewPageBeforeNextContent = false;
			$this->addPage();
		}
		$pageDim = $this->getPageDimensions();
		
		if(empty($options['position']['object'])) {
			$options['position']['object'] = 'page';
		}
		
		// Determin the x and y coordinates
		if($options['position']['object'] == 'last_position') {
			$x = $this->x+$options['position']['x'];
			$y = $this->y+$options['position']['y'];
		}
		elseif($options['position']['object'] == 'page') {
			
			switch($options['position']['corner']) {
				default:
				case 'top_left':
					$x = $options['position']['x']+$this->lMargin;
					$y = $options['position']['y']+$this->tMargin;
					break;
				
				case 'top_right':
					$x = $options['position']['x'] + $pageDim['wk'] - $this->rMargin;
					$y = $options['position']['y'] + $this->tMargin;		
					break;
				
				case 'bottom_left':
					$x = $options['position']['x'] + $this->rMargin;
					$y = $options['position']['y'] + $pageDim['hk'] - $this->bMargin;
				
					break;
				
				case 'bottom_right': 
					$x = $options['position']['x'] + $pageDim['wk'] - $this->rMargin;
					$y = $options['position']['y'] + $pageDim['hk'] - $this->bMargin;
				
					break;
			}
		}
		elseif($options['position']['object'] == 'self' or preg_match('/field\_(.*)/', $options['position']['object'], $rs)) {
			if($options['position']['object'] == 'self') {
				$relative_to_element = $key;
			}
			else {
				$relative_to_element = $rs[1];
			}
			
			
			if(isset($this->elements[$relative_to_element])){
				
				switch($options['position']['corner']) {
					default:
					case 'top_left':
						$x = $options['position']['x'] + $this->elements[$relative_to_element]['x'];
						$y = $options['position']['y'] + $this->elements[$relative_to_element]['y'];
						break;
				
					case 'top_right':
						$x = $options['position']['x'] + $this->elements[$relative_to_element]['x'] + $this->elements[$relative_to_element]['width'];
						$y = $options['position']['y'] + $this->elements[$relative_to_element]['y'];		
						break;
				
					case 'bottom_left':
						$x = $options['position']['x'] + $this->elements[$relative_to_element]['x'];
						$y = $options['position']['y'] + $this->elements[$relative_to_element]['y'] + $this->elements[$relative_to_element]['height'];
				
						break;
				
					case 'bottom_right': 
						$x = $options['position']['x'] + $this->elements[$relative_to_element]['x'] + $this->elements[$relative_to_element]['width'];
						$y = $options['position']['y'] + $this->elements[$relative_to_element]['y'] + $this->elements[$relative_to_element]['height'];
				
						break;
				}
				
			}
			else {
				$x = $this->x;
				$y = $this->y;
			}
			
		}
		
		// No position match
		else {
			// Render and then return
			if(is_object($view) && $key != NULL ) {
				$content = $view->field[$key]->theme($row);
			}

			return;
		}
		
		$this->SetX($x);
		$this->SetY($y);
		
		// Render the content if it is not already:
		if(is_object($view) && $key != NULL ) {
			$content = $view->field[$key]->theme($row);
		}
		else {
			$content = $row;
		}
		if(!empty($view->field[$key]->options['exclude'])) {
			return '';
		}
		
		$font_size = empty($options['text']['font_size']) ? $this->defaultFontSize : $options['text']['font_size'] ;
		$font_family = ($options['text']['font_family'] == 'default' || empty($options['text']['font_family'])) ? $this->defaultFontFamily : $options['text']['font_family'];
		$font_style = is_array($options['text']['font_style']) ? $options['text']['font_style'] : $this->defaultFontStyle;
		$textColor = !empty($options['text']['color']) ? $this->convertHexColorToArray($options['text']['color']) : $this->convertHexColorToArray($this->defaultFontColor);
		
		
		$w = $options['position']['width'];
		$h = $options['position']['height'];
		$border = 0;
		$align = isset($options['text']['align']) ? $options['text']['align'] : $this->defaultTextAlign;
		$fill = 0;
		$ln = 1;
		$reseth = true;
		$stretch = 0;
		$ishtml = isset($options['render']['is_html']) ? $options['render']['is_html'] : 1;
		$autopadding = true;
		$maxh = 0;
		$valign = 'T';
		$fitcell = false;
		
		// Run eval before
		eval($options['render']['eval_before']);
		
		// Set Text Color
		$this->SetTextColorArray($textColor);
		
		// Set font
		$this->SetFont($font_family, implode('', $font_style), $font_size);
								
		// Write the content of a field to the pdf file:
		$this->MultiCell($w, $h, $content, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
		
		// Reset font to default
		$this->SetFont($this->defaultFontFamily, implode('', $this->defaultFontStyle), $this->defaultFontSize);
		
		// Write Coordinates of element
		$this->elements[$key] = array(
			'x' => $x,
			'y' => $y,
			'width' => empty($w) ? ($pageDim['wk'] - $this->rMargin-$x) : $w,
			'height' => $this->y - $y
		);
		
		// Run eval after
		eval($options['render']['eval_after']);
		
	}
	
	public function drawTable(&$view, $options) {
		
		$rows = $view->result;
		$columns = $view->field;
		$pageDim = $this->getPageDimensions();
		
		// Set draw point to the indicated position:
		if(isset($options['position']['x']) && !empty($options['position']['x'])) {
			//$this->SetX($options['position']['x']);
		}
		
		if(isset($options['position']['y']) && !empty($options['position']['y'])) {
			//$this->SetY($options['position']['y']);
		}
		
		if(isset($options['position']['width']) && !empty($options['position']['width'])) {
			$width = $options['position']['width'];
		}
		else {
			$width = $pageDim['wk'] - $this->rMargin - $this->x;
		}
		
		$sumWidth = 0;
		$numerOfColumnsWithoutWidth = 0;
		// Set the definitiv width of a column
		foreach($columns as $id => $columnName) {
			if(isset($option['info'][$id]['position']['width']) && !empty($option['info'][$id]['position']['width'])){
				$sumWidth += $option['info'][$id]['position']['width'];
			}
			else {
				$numerOfColumnsWithoutWidth++;
			}
		}
		if($numerOfColumnsWithoutWidth > 0) {
			$defaultColumnWidth = ($width - $sumWidth) / $numerOfColumnsWithoutWidth;
		}
		else {
			$defaultColumnWidth = 0;
		}
		
		// Print header:
		$y = $this->y;
		$x = $this->x;
		
		$page = $this->getPage();
		if($page == 0) {
			$this->addPage();
			$page = $this->getPage();
		}
		
		
		foreach($columns as $id => $column) {
			
			if (!empty($column->options['exclude'])) {
				continue;
			}

			$headerOptions = $options['info'][$id]['header_style'];
			
			if(isset($option['info'][$id]['position']['width']) && !empty($option['info'][$id]['position']['width'])){
				$headerOptions['position']['width'] = $option['info'][$id]['position']['width'];
			}
			else {
				$headerOptions['position']['width'] = $defaultColumnWidth;
			}
			$headerOptions['position']['object'] = 'last_position';
			$this->SetY($y);
			$this->SetX($x);
			$this->setPage($page);
		
			
			$this->drawContent($column->options['label'], $headerOptions);
			$x += $headerOptions['position']['width'];
		}
		
		foreach($rows as $row) {
			// Print header:
			$y = $this->y;
			$x = $this->x;
			$page = $this->getPage();
			foreach($columns as $id => $column) {
			
				if (!empty($column->options['exclude'])) {
					// Render the element, but dont print any thing
					$view->field[$key]->theme($row);
					continue;
				}

				$bodyOptions = $options['info'][$id]['body_style'];
			
				if(isset($option['info'][$id]['position']['width']) && !empty($option['info'][$id]['position']['width'])){
					$bodyOptions['position']['width'] = $option['info'][$id]['position']['width'];
				}
				else {
					$bodyOptions['position']['width'] = $defaultColumnWidth;
				}
				$bodyOptions['position']['object'] = 'last_position';
				$this->SetY($y);
				$this->SetX($x);
				$this->setPage($page);
			
				$this->drawContent($row, $bodyOptions, $view, $id);
				$x += $headerOptions['position']['width'];
			}
			
		}
		
		
		
	}
	
	
	/**
	 * This method adds a existing PDF document to the current document. If 
	 * the file does not exists this method will return 0. In all other cases 
	 * it will returns the number of the added pages.
	 *
	 * @param $path string Path to the file
	 * @return integer Number of added pages
	 */
	public function addPdfDocument($path) {
		if(empty($path) || !file_exists($path)) {
			return 0;
		}
		
		$numberOfPages = $this->setSourceFile($path);
		for($i = 1; $i <= $numberOfPages; $i++) {
			
			$dim = $this->getTemplateSize($i);
			$format[0] = $dim['w'];
			$format[1] = $dim['h'];
			
			if($dim['w'] > $dim['h'])
			{
				$orientation = 'L';
			}
			else
			{
				$orientation = 'P';
			}
			$this->setPageFormat($format, $orientation);
			parent::addPage();
			
			// Ensure that all new content is printed to a new page
			$this->y = 0;

			$page = $this->importPage($i);
			$this->useTemplate($page,0,0);
			$this->addNewPageBeforeNextContent = true;		
		}
				
		return $numberOfPages;
		
	}
	
	public function resetRowPageNumber() {
		$this->rowContentPageNumber = 0;
	}
	
	public function addPage($path = NULL, $reset = false, $numbering = 'main') {
		
		$this->mainContentPageNumber++;
		$this->rowContentPageNumber++;
		
		// Reset without any template
		if((empty($path) || !file_exists($path)) && $reset == true) {
			parent::addPage();
			$this->setPageFormat($this->defaultFormat, $this->defaultOrientation);
			return;
		}
		
		$files = $this->defaultPageTemplateFiles;
		
		// Reset with new template
		if($reset) {
			$files = array();
		}
		
		if($path != NULL) {
			$files[] = array('path' => $path, 'numbering' => $numbering);
		}
		$format = false;
		foreach($files as $file) {
			if(!empty($file['path']) && file_exists($file['path'])) {
				$path = realpath($file['path']);

				$numberOfPages = $this->setSourceFile($path);
				if($file['numbering'] == 'row')  {
					$index = min($this->rowContentPageNumber, $numberOfPages);
				}
				else {
					$index = min($this->mainContentPageNumber, $numberOfPages);
				}
				
		
				$page = $this->importPage($index);
		
				// ajust the page format (only for the first template)
				if($format == false)
				{
					
					$dim = $this->getTemplateSize($index);
					$format[0] = $dim['w'];
					$format[1] = $dim['h'];
					//$this->setPageFormat($format);
					if($dim['w'] > $dim['h'])
					{
						$orientation = 'L';
					}
					else
					{
						$orientation = 'P';
					}
					$this->setPageFormat($format, $orientation);
					parent::addPage();
				}
				
				// Apply the template
				$this->useTemplate($page,0,0);
			}
		}
		
		// if all paths were empty, ensure that at least the page is added
		if($format == false) {
			parent::addPage();
			$this->setPageFormat($this->defaultFormat, $this->defaultOrientation);
		}
			
		
		
		
	}
	
	public static function getAvailableTemplates() {
		if(self::$templateList != NULL) {
			return self::$templateList;
		}
		
		$files_path = file_directory_path();
		$template_dir = variable_get('views_pdf_template_path','views_pdf_templates');
		$dir = $files_path.'/'.$template_dir;
		$templatesFiles = file_scan_directory($dir, '.pdf', array('.', '..', 'CVS'), 0, FALSE, 'filename', 0, 1);
		
		$templates = array();
		
		foreach($templatesFiles as $file) {
			$templates[$file->name] = $file->name;
		}
		
		self::$templateList = $templates;
		
		return $templates;
	
	}
	
	public static function getTemplatePath($template, $row = null, $view = null) {
		if(empty($template)) {
			return '';
		}
		
		if($row != null && $view != null && !preg_match('/\.pdf/', $template)) {
			return $view->field[$template]->theme($row);
		}
	
		$files_path = file_directory_path();
		$template_dir = variable_get('views_pdf_template_path','views_pdf_templates');
		$dir = $files_path.'/'.$template_dir;
		
		return $dir . '/' . $template.'.pdf';
		
	}


	
	public static function getAvailableFonts() {
		if(self::$fontList != NULL) {
			return self::$fontList;
		}
		
		// Get all pdf files with the font list: K_PATH_FONTS
		$fonts = file_scan_directory(K_PATH_FONTS, '.php', array('.', '..', 'CVS'), 0, FALSE, 'filename', 0, 1);
		$cache = cache_get('views_pdf_cached_fonts');
		$cached_font_mapping = $cache->data;
		
		if(is_array($cached_font_mapping) ) {
			$font_mapping = array_merge(self::$defaultFontList, $cached_font_mapping);
		}
		else {
			$font_mapping = self::$defaultFontList;
		}
		
		foreach($fonts as $font) {
			if(!isset($font_mapping[$font->name])) {
				$font_mapping[$font->name] = self::getFontNameByFileName($font->filename);
			}
		}
		
		asort($font_mapping);
		
		cache_set('views_pdf_cached_fonts', $font_mapping);
		
		// Remove all fonts without name
		foreach($font_mapping as $key =>$font) {
			if(empty($font)) {
				unset($font_mapping[$key]);
			}
				
		}
		
		self::$fontList = $font_mapping;
		
		return $font_mapping;
	}
	
	public static function getAvailableFontsCleanList() {
		if(self::$fontListClean != NULL) {
			return self::$fontListClean;
		}
		
		$clean = self::getAvailableFonts();

		foreach($clean as $key => $font) {
			
			// Unset bold, italic, italic/bold fonts
			unset($clean[ ($key.'b') ]);
			unset($clean[ ($key.'bi') ]);
			unset($clean[ ($key.'i') ]);
			
		}
		
		self::$fontListClean = $clean;
		
		return $clean;
	}
	
	protected static function getFontNameByFileName($path) {
		include $path;
		return $name;
	}
}

