<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( isset( $this->options['page_size'] ) ) {
	$pagesize = ( $this->options['page_size'] );
} else {
	$pagesize = PDF_PAGE_FORMAT;
}
if ( isset( $this->options['unitmeasure'] ) ) {
	$unit = ( $this->options['unitmeasure'] );
} else {
	$unit = PDF_UNIT;
}
if ( isset( $this->options['page_orientation'] ) ) {
	$orientation = ( $this->options['page_orientation'] );
} else {
	$orientation = PDF_PAGE_ORIENTATION;
}
$pdf = new CUSTOMPDF ( $orientation, $unit, $pagesize, true, 'UTF-8', false );
if (!empty ( $this->options ['rtl_support'] )) {//die();
				// set some language dependent data:
				$lg = Array();
				$lg['a_meta_charset'] = 'UTF-8';
				$lg['a_meta_dir'] = 'rtl';
				$lg['a_meta_language'] = 'fa';
				$lg['w_page'] = 'page';
					
				// set some language-dependent strings (optional)
				$pdf->setLanguageArray($lg);
				$pdf->setRTL(true);
}
$pdf->SetCreator ( 'WP Post to PDF plugin by CedCommerce with ' . PDF_CREATOR );
$pdf->SetAuthor ( get_bloginfo ( 'name' ) );

// $pdf->SetTitle ( apply_filters ( 'the_post_title', $post->post_title ) );
	
// logo width calculation
			if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) != 'None' and !empty ( $this->options ['logo_img_url'] )) {
				
				if ($this->options ['page_header'] == "upload-image") {
					$logoImage_url = $this->options ['logo_img_url'];
				}
				$infologo = getimagesize ( $logoImage_url );
				try {
					if (isset ( $this->options ['imagefactor'] )) {
						$logo_width = @ ( int ) (($this->options ['imagefactor'] * $infologo [0]) / $infologo [1]);
					} else {
						$logo_width = @ ( int ) ((12 * $infologo [0]) / $infologo [1]);
					}
				} 
				catch(Exception $e){
				  throw new Exception("Invalid Size Image..");
				  echo "Exception:".$e->getMessage();
				}
			}
			
			if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) == 'None') {
			
				$logoImage_url="";
				$logo_width="";
			}
			
			// for PHP 5.4 or below set default header data
			$blog_name = get_bloginfo ( 'name' );
			$bolg_description = get_bloginfo ( 'description' );
			$home_url = home_url ();
			$ptpdfoption_status = get_option ( PTPDF_PREFIX );
			if(isset($ptpdfoption_status)){
				$name_status=isset($ptpdfoption_status['show_site_name'])? $ptpdfoption_status['show_site_name']: '' ;
				$desc_status=isset($ptpdfoption_status['show_site_descR'])? $ptpdfoption_status['show_site_descR']: '' ;
				$url_status=isset($ptpdfoption_status['show_site_URL'])? $ptpdfoption_status['show_site_URL']: '' ;
			}
			// for PHP 5.4 or below set default header data
			if (version_compare ( phpversion (), '5.4.0', '<' )) {
				if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
				}
			} elseif(version_compare ( phpversion (), '5.4.0', '>' )) {
				if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
				}
			}
	
// set header and footer fonts
if(($this->options['header_font_size']) > 0) {
	$header_font_size = $this->options ['header_font_size'];
} else {
	$header_font_size = 10;
}
if(($this->options['footer_font_size']) > 0) {
	$footer_font_size = $this->options ['footer_font_size'];
} else {
	$footer_font_size = 10;
}
$pdf->setHeaderFont ( array (
		$this->options ['header_font_pdf'],
		'',
		$header_font_size 
) );
$pdf->setFooterFont ( array (
		$this->options ['footer_font_pdf'],
		'',
		$footer_font_size 
) );
	
$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );

if (isset($this->options ['marginLeft'])) {
	$pdf->SetLeftMargin ( $this->options ['marginLeft'] );
} else {
	$pdf->SetLeftMargin ( PDF_MARGIN_LEFT );
}
	
if (isset($this->options ['marginRight'])) {
	$pdf->SetRightMargin ( $this->options ['marginRight'] );
} else {
	$pdf->SetRightMargin ( PDF_MARGIN_RIGHT );
}
	
if (isset($this->options ['marginTop'])) {
	$pdf->SetTopMargin ( $this->options ['marginTop'] );
} else {
	$pdf->SetTopMargin ( PDF_MARGIN_TOP );
}
if ((isset($this->options ['logomTop']))) {
	$pdf->SetHeaderMargin ( $this->options ['logomTop'] );
} else {
	$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
}

if (isset($this->options ['footer_font_margin'] )) {
	$pdf->SetFooterMargin ( $this->options ['footer_font_margin'] );
	// set auto page breaks
	$pdf->SetAutoPageBreak ( TRUE,  $this->options ['footer_font_margin']  );
} else {
	$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
	// set auto page breaks
	$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_FOOTER );
}
if (! empty ( $this->options ['bullet_img_url'] )) {
	$temp = $this->options ['bullet_img_url'];
	$temp = end ( explode ( '/', $temp ) );
	$temp = end ( explode ( '.', $temp ) );
	$listsymbol = 'img|' . $temp . '|' . $this->options ['custom_image_width'] . '|' . $this->options ['custom_image_height'] . '|' . $this->options ['bullet_img_url'];
	$pdf->setLIsymbol ( $listsymbol );
}

// set image scale factor

if ($this->options ['imageScale'] > 0) {
	$pdf->setImageScale ( $this->options ['imageScale'] );
} else {
	$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
}
	
// set default font subsetting mode
$pdf->setFontSubsetting( true );
	
$pdf->SetFont( $this->options['content_font_pdf'], '', $this->options['content_font_size'], '', true );
	
foreach ($post_ids as $post_id) {
	$post = get_post ( $post_id );
	
	$content = $post->the_content;
	{
		if (has_shortcode ( $content, 'wpppdf' )) {
			if (! $this->options [$post->post_type])
				return false;
		}
	}
	
// 	$pdf->AddPage ();
	
	if ($this->options ['fontStretching']) {
		$pdf->setFontStretching($this->options ['fontStretching']);
	}
	if ($this->options ['fontSpacig']) {
		$pdf->setFontSpacing($this->options ['fontSpacig']);
	}
	$page_format = array();
	if ($this->options ['set_rotation']) {
		$page_format['Rotate'] = $this->options ['set_rotation'];
	} else {
		$page_format['Rotate'] = 0;
	}
	$pdf->AddPage($this->options ['page_orientation'], $page_format, false, false);
	$pdf->Bookmark($post->post_title, 1, -1, '', '', array(0,0,0));
	$html = '';
	if ($this->options ['applyCSS']) {
		$html .= '<style>' . $this->options ['Customcss'] . '</style>';
	}
	$html .= "<body>";
	$html .= "<h1 style=\"text-align:center\">".apply_filters ( 'the_post_title', $pdf_title )."</h1>";
	if (isset ( $this->options ['postCategories'] )) {
		$categories = get_the_category_list ( ', ', '', $post );
		if ($categories) {
			$html .= '<p><strong>Categories : </strong>' . $categories . '</p>';
		}
	}
	// Display tag list is set in config
	if (isset ( $this->options ['postTags'] )) {
		$tags = get_the_tags ( $post->the_tags );
		if ($tags) {
			$html .= '<p><strong>Tagged as : </strong>';
			foreach ( $tags as $tag ) {
				$tag_link = get_tag_link ( $tag->term_id );
				$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
				if (next ( $tags )) {
					$html .= ', ';
				}
			}
			$html .= '</p>';
		}
	}
	// Display date if set in config
	if (isset ( $this->options ['postDate'] )) {
		$date = get_the_date ( $post->the_date, $post_id );
		$html .= '<p><strong>Date : </strong>' . $date . '</p>';
	}
	
	$html .= '<h1>' . html_entity_decode ( $post->post_title, ENT_QUOTES ) . '</h1>';
	
	// Display feachered image if set in config on page/post
	if (isset ( $this->options ['show_feachered_image'] )) {
		if (has_post_thumbnail ( $post->ID )) {
			$html .= get_the_post_thumbnail ( $post->ID );
		}
	}
	$html .= htmlspecialchars_decode ( htmlentities ( $post->post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
	$html .="</body>";
	$dom = new simple_html_dom ();
	$dom->load ( $html );
	
	foreach ( $dom->find ( 'img' ) as $e ) {
			
			// Start Image check by om
		$exurl = ''; // external streams
		$imsize = FALSE;
		$file = $e->src;
		// check if we are passing an image as file or string
		if ($file [0] === '@') {
			// image from string
			$imgdata = substr ( $file, 1 );
		} else { // image file
			if ($file {0} === '*') {
				// image as external stream
				$file = substr ( $file, 1 );
				$exurl = $file;
			}
			// check if is local file
			if (! @file_exists ( $file )) {
				// encode spaces on filename (file is probably an URL)
				$file = str_replace ( ' ', '%20', $file );
			}
			if (@file_exists ( $file )) {
				// get image dimensions
				$imsize = @getimagesize ( $file );
			}
			if ($imsize === FALSE) {
				$imgdata = TCPDF_STATIC::fileGetContents ( $file );
			}
		}
		if (isset ( $imgdata ) and ($imgdata !== FALSE) and (strpos ( $file, '__tcpdf_img' ) === FALSE)) {
			// copy image to cache
			$original_file = $file;
			$file = TCPDF_STATIC::getObjFilename ( 'img' );
			$fp = fopen ( $file, 'w' );
			fwrite ( $fp, $imgdata );
			fclose ( $fp );
			unset ( $imgdata );
			$imsize = @getimagesize ( $file );
			
		}
		if ($imsize === FALSE) {
			$e->outertext = '';
		} else {
			
			// End Image Check
			if (preg_match ( '/alignleft/i', $e->class )) {
				$imgalign = 'left';
			} elseif (preg_match ( '/alignright/i', $e->class )) {
				$imgalign = 'right';
			} elseif (preg_match ( '/aligncenter/i', $e->class )) {
				$imgalign = 'center';
				$htmlimgalign = 'middle';
			} else {
				$imgalign = 'none';
			}
			
			$e->class = null;
			$e->align = $imgalign;
			if (isset ( $htmlimgalign )) {
				$e->style = 'float:' . $htmlimgalign;
			} else {
				$e->style = 'float:' . $imgalign;
			}
			
			if (strtolower ( substr ( $e->src, - 4 ) ) == '.svg') {
				$e->src = null;
				$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
			} else {
				$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
			}
		}
	}
	$html = $dom->save ();
	$dom->clear ();
	
	$pdf->setFormDefaultProp ( array (
			'lineWidth' => 1,
			'borderStyle' => 'solid',
			'fillColor' => array (
					255,
					255,
					200 
			),
			'strokeColor' => array (
					255,
					128,
					128 
			) 
	) );
	// Print text using writeHTML
	$pdf->writeHTML ( $html, true, 0, true, 0 );
}
if(isset( $this->options['add_watermark'] )) {
	$no_of_pages = $pdf->getNumPages ();
	for($i = 1; $i <= $no_of_pages; $i ++) {
		$pdf->setPage ( $i );
		
		// Get the page width/height
		$myPageWidth = $pdf->getPageWidth ();
		$myPageHeight = $pdf->getPageHeight ();
		
		// Find the middle of the page and adjust.
		$myX = ($myPageWidth / 2) - 75;
		$myY = ($myPageHeight / 2) + 25;
		
		// Set the transparency of the text to really light
		$pdf->SetAlpha ( 0.09 );
		
		$pdf->StartTransform ();
		$rotate_degr = isset ( $this->options ['rotate_water'] ) ? $this->options ['rotate_water'] : '45';
		$pdf->Rotate ( $rotate_degr, $myX, $myY );
		$water_font = isset ( $this->options ['water_font'] ) ? $this->options ['water_font'] : 'courier';
		$pdf->SetFont ( $water_font, "", 30 );
		$watermark_text = isset ( $this->options ['watermark_text'] ) ? $this->options ['watermark_text'] : '';
		$pdf->Text ( $myX, $myY, $watermark_text );
		$pdf->StopTransform ();
		
		// Reset the transparency to default
		$pdf->SetAlpha ( 1 );
	}
}
if (isset ( $this->options ['add_watermark_image'] )) {
	if (! empty ( $this->options ['background_img_url'] )) {
		$no_of_pages = $pdf->getNumPages ();
		for($i = 1; $i <= $no_of_pages; $i ++) {
			$pdf->setPage ( $i );
			
			$myPageWidth = $pdf->getPageWidth ();
			$myPageHeight = $pdf->getPageHeight ();
			$myX = ($myPageWidth / 2) - 50; // WaterMark Positioning
			$myY = ($myPageHeight / 2) - 40;
			$ImageT = isset ( $this->options ['water_img_t'] ) ? $this->options ['water_img_t'] : '';
			// Set the transparency of the text to really light
			$pdf->SetAlpha ( $ImageT );
			
			// Rotate 45 degrees and write the watermarking text
			$pdf->StartTransform ();
			$ImageW = isset ( $this->options ['water_img_h'] ) ? $this->options ['water_img_h'] : '';
			$ImageH = isset ( $this->options ['water_img_w'] ) ? $this->options ['water_img_w'] : '';
			
			$watermark_img = isset ( $this->options ['background_img_url'] ) ? $this->options ['background_img_url'] : '';
			$pdf->Image ( $watermark_img, $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150 );
			
			$pdf->StopTransform ();
			
			$pdf->SetAlpha ( 1 );
		}
	}
}
if (! is_dir ( CACHE_DIR )) {
	mkdir ( CACHE_DIR, 0777, true );
}
$pdf->Output ( $filePath, 'F' );
