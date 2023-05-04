<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * pdfheader.php
 * 
 * This file contains header functions
 * 
 */
if (! class_exists ( 'TCPDF' )) {
	require_once PTPDF_PATH . '/tcpdf/tcpdf.php';
}
/* if($this->options['page_header']=="None") {
	define('PRINT_HEADER','1');
} */
class CUSTOMPDF extends TCPDF {
	public function header() {
		if(defined('PRINT_HEADER')) {
			$this->setPrintHeader ( false );
		} else {
			parent::header();
		}
	}
	public function Footer() {
		$options = get_option ( PTPDF_PREFIX );
		
		if(isset($options['custom_footer_option'])) {
			if(!empty($options['custom_footer']))
			$this->writeHTMLCell ($options['footer_cell_width'], $options['footer_min_height'], $options['footer_lcornerX'], $options['footer_font_lcornerY'], $options['custom_footer'], '',0, $options['footer_cell_fill'], true, $options['footer_align'], $options['footer_cell_auto_padding']);
				
		} else {
			// call parent footer method for default footer
			parent::Footer();
		}
	}
}