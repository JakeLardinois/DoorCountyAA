
<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly?>
<div class="wrap">
	<div id="ptpdf-options" class="ptpdf-option wrap">
		
	<?php
	settings_fields ( PTPDF_PREFIX .'_options' );
	$ptpdfoptions = get_option ( PTPDF_PREFIX );
	//echo "<pre>";print_r($ptpdfoptions);
	?>
	<?php
	$fonts = array (
			'Courier' => 'courier',
			'Courier Bold' => 'courierB',
			'Courier Bold Italic' => 'courierBI',
			'Courier Italic' => 'courierI',
			'Helvetica' => 'helvetica',
			'Helvetica Bold' => 'helveticaB',
			'Helvetica Bold Italic' => 'helveticaBI',
			'Helvetica Italic' => 'helveticaI',
			'Symbol' => 'symbol',
			'Times New Roman' => 'times',
			'Times New Roman Bold' => 'timesB',
			'Times New Roman Bold Italic' => 'timesBI',
			'Times New Roman Italic' => 'timesI',
			'Zapf Dingbats' => 'zapfdingbats',
	);
	$custom_fonts = get_option('ptp_custom_fonts', array());
	if(!empty($custom_fonts)) {
		$fonts = array_merge( $fonts, $custom_fonts );
	}
	
	$page_size = array (
			'ISO 216 A Series + 2 SIS 014711 extensions (default: A4)' => 'A4',
			'A0 (841x1189 mm ; 33.11x46.81 in)' => 'A0',
			'A1 (594x841 mm ; 23.39x33.11 in)' => 'A1',
			'A2 (420x594 mm ; 16.54x23.39 in)' => 'A2',
			'A3 (297x420 mm ; 11.69x16.54 in)' => 'A3',
			'A4 (210x297 mm ; 8.27x11.69 in)' => 'A4',
			'A5 (148x210 mm ; 5.83x8.27 in)' => 'A5',
			'A6 (105x148 mm ; 4.13x5.83 in)' => 'A6',
			'A7 (74x105 mm ; 2.91x4.13 in)' => 'A7',
			'A8 (52x74 mm ; 2.05x2.91 in)' => 'A8',
			'A9 (37x52 mm ; 1.46x2.05 in)' => 'A9',
			'A10 (26x37 mm ; 1.02x1.46 in)' => 'A10',
			'A11 (18x26 mm ; 0.71x1.02 in)' => 'A11',
			'A12 (13x18 mm ; 0.51x0.71 in)' => 'A12',
			'ISO 216 B Series + 2 SIS 014711 extensions (default: B4)' => 'B4',
			'B0 (1000x1414 mm ; 39.37x55.67 in)' => 'B0',
			'B1 (707x1000 mm ; 27.83x39.37 in)' => 'B1',
			'B2 (500x707 mm ; 19.69x27.83 in)' => 'B2',
			'B3 (353x500 mm ; 13.90x19.69 in)' => 'B3',
			'B4 (250x353 mm ; 9.84x13.90 in)' => 'B4',
			'B5 (176x250 mm ; 6.93x9.84 in)' => 'B5',
			'B6 (125x176 mm ; 4.92x6.93 in)' => 'B6',
			'B7 (88x125 mm ; 3.46x4.92 in)' => 'B7',
			'B8 (62x88 mm ; 2.44x3.46 in)' => 'B8',
			'B9 (44x62 mm ; 1.73x2.44 in)' => 'B9',
			'B10 (31x44 mm ; 1.22x1.73 in)' => 'B10',
			'B11 (22x31 mm ; 0.87x1.22 in)' => 'B11',
			'B12 (15x22 mm ; 0.59x0.87 in)' => 'B12',
			'ISO 216 C Series + 2 SIS 014711 extensions + 2 EXTENSION (default: C4)' => 'C4',
			'C0 (917x1297 mm ; 36.10x51.06 in)' => 'C0',
			'C1 (648x917 mm ; 25.51x36.10 in)' => 'C1',
			'C2 (458x648 mm ; 18.03x25.51 in)' => 'C2',
			'C3 (324x458 mm ; 12.76x18.03 in)' => 'C3',
			'C4 (229x324 mm ; 9.02x12.76 in)' => 'C4',
			'C5 (162x229 mm ; 6.38x9.02 in)' => 'C5',
			'C6 (114x162 mm ; 4.49x6.38 in)' => 'C6',
			'C7 (81x114 mm ; 3.19x4.49 in)' => 'C7',
			'C8 (57x81 mm ; 2.24x3.19 in)' => 'C8',
			'C9 (40x57 mm ; 1.57x2.24 in)' => 'C9',
			'C10 (28x40 mm ; 1.10x1.57 in)' => 'C10',
			'C11 (20x28 mm ; 0.79x1.10 in)' => 'C11',
			'C12 (14x20 mm ; 0.55x0.79 in)' => 'C12',
			'C76 (81x162 mm ; 3.19x6.38 in)' => 'C76',
			'DL (110x220 mm ; 4.33x8.66 in)' => 'DL',
			'SIS 014711 E Series (default: E4)' => 'E4',
			'E0 (879x1241 mm ; 34.61x48.86 in)' => 'E0',
			'E1 (620x879 mm ; 24.41x34.61 in)' => 'E1',
			'E2 (440x620 mm ; 17.32x24.41 in)' => 'E2',
			'E3 (310x440 mm ; 12.20x17.32 in)' => 'E3',
			'E4 (220x310 mm ; 8.66x12.20 in)' => 'E4',
			'E5 (155x220 mm ; 6.10x8.66 in)' => 'E5',
			'E6 (110x155 mm ; 4.33x6.10 in)' => 'E6',
			'E7 (78x110 mm ; 3.07x4.33 in)' => 'E7',
			'E8 (55x78 mm ; 2.17x3.07 in)' => 'E8',
			'E9 (39x55 mm ; 1.54x2.17 in)' => 'E9',
			'E10 (27x39 mm ; 1.06x1.54 in)' => 'E10',
			'E11 (19x27 mm ; 0.75x1.06 in)' => 'E11',
			'E12 (13x19 mm ; 0.51x0.75 in)' => 'E12',
			'SIS 014711 G Series (default: G4)' => 'G4',
			'G0 (958x1354 mm ; 37.72x53.31 in)' => 'G0',
			'G1 (677x958 mm ; 26.65x37.72 in)' => 'G1',
			'G2 (479x677 mm ; 18.86x26.65 in)' => 'G2',
			'G3 (338x479 mm ; 13.31x18.86 in)' => 'G3',
			'G4 (239x338 mm ; 9.41x13.31 in)' => 'G4',
			'G5 (169x239 mm ; 6.65x9.41 in)' => 'G5',
			'G6 (119x169 mm ; 4.69x6.65 in)' => 'G6',
			'G7 (84x119 mm ; 3.31x4.69 in)' => 'G7',
			'G8 (59x84 mm ; 2.32x3.31 in)' => 'G8',
			'G9 (42x59 mm ; 1.65x2.32 in)' => 'G9',
			'G10 (29x42 mm ; 1.14x1.65 in)' => 'G10',
			'G11 (21x29 mm ; 0.83x1.14 in)' => 'G11',
			'G12 (14x21 mm ; 0.55x0.83 in)' => 'G12',
			'ISO Press (default: RA4)' => 'RA4',
			'RA0 (860x1220 mm ; 33.86x48.03 in)' => 'RA0',
			'RA1 (610x860 mm ; 24.02x33.86 in)' => 'RA1',
			'RA2 (430x610 mm ; 16.93x24.02 in)' => 'RA2',
			'RA3 (305x430 mm ; 12.01x16.93 in)' => 'RA3',
			'RA4 (215x305 mm ; 8.46x12.01 in)' => 'RA4',
			'SRA0 (900x1280 mm ; 35.43x50.39 in)' => 'SRA0',
			'SRA1 (640x900 mm ; 25.20x35.43 in)' => 'SRA1',
			'SRA2 (450x640 mm ; 17.72x25.20 in)' => 'SRA2',
			'SRA3 (320x450 mm ; 12.60x17.72 in)' => 'SRA3',
			'SRA4 (225x320 mm ; 8.86x12.60 in)' => 'SRA4',
			'German DIN 476 (default: 4A0)' => '4A0',
			'4A0 (1682x2378 mm ; 66.22x93.62 in)' => '4A0',
			'2A0 (1189x1682 mm ; 46.81x66.22 in)' => '2A0',
			'Variations on the ISO Standard (default: A4_EXTRA)' => 'A4_EXTRA',
			'A2_EXTRA (445x619 mm ; 17.52x24.37 in)' => 'A2_EXTRA',
			'A3+ (329x483 mm ; 12.95x19.02 in)' => 'A3+',
			'A3_EXTRA (322x445 mm ; 12.68x17.52 in)' => 'A3_EXTRA',
			'A3_SUPER (305x508 mm ; 12.01x20.00 in)' => 'A3_SUPER',
			'SUPER_A3 (305x487 mm ; 12.01x19.17 in)' => 'SUPER_A3',
			'A4_EXTRA (235x322 mm ; 9.25x12.68 in)' => 'A4_EXTRA',
			'A4_SUPER (229x322 mm ; 9.02x12.68 in)' => 'A4_SUPER',
			'SUPER_A4 (227x356 mm ; 8.94x14.02 in)' => 'SUPER_A4',
			'A4_LONG (210x348 mm ; 8.27x13.70 in)' => 'A4_LONG',
			'F4 (210x330 mm ; 8.27x12.99 in)' => 'F4',
			'SO_B5_EXTRA (202x276 mm ; 7.95x10.87 in)' => 'SO_B5_EXTRA',
			'A5_EXTRA (173x235 mm ; 6.81x9.25 in)' => 'A5_EXTRA',
			'ANSI Series (default: ANSI_A)' => 'ANSI_A',
			'ANSI_E (864x1118 mm ; 34.00x44.00 in)' => 'ANSI_E',
			'ANSI_D (559x864 mm ; 22.00x34.00 in)' => 'ANSI_D',
			'ANSI_C (432x559 mm ; 17.00x22.00 in)' => 'ANSI_C',
			'ANSI_B (279x432 mm ; 11.00x17.00 in)' => 'ANSI_B',
			'ANSI_A (216x279 mm ; 8.50x11.00 in)' => 'ANSI_A',
			'Traditional \'Loose\' North American Paper Sizes (default: LETTER)' => 'LETTER',
			'LEDGER, USLEDGER (432x279 mm ; 17.00x11.00 in)' => 'LEDGER',
			'TABLOID, USTABLOID, BIBLE, ORGANIZERK (279x432 mm ; 11.00x17.00 in)' => 'TABLOID',
			'LETTER, USLETTER, ORGANIZERM (216x279 mm ; 8.50x11.00 in)' => 'LETTER',
			'LEGAL, USLEGAL (216x356 mm ; 8.50x14.00 in)' => 'LEGAL',
			'GLETTER, GOVERNMENTLETTER (203x267 mm ; 8.00x10.50 in)' => 'GLETTER',
			'JLEGAL, JUNIORLEGAL (203x127 mm ; 8.00x5.00 in)' => 'JLEGAL',
			'Other North American Paper Sizes (default: FOLIO)' => 'FOLIO',
			'QUADDEMY (889x1143 mm ; 35.00x45.00 in)' => 'QUADDEMY',
			'SUPER_B (330x483 mm ; 13.00x19.00 in)' => 'SUPER_B',
			'QUARTO (229x279 mm ; 9.00x11.00 in)' => 'QUARTO',
			'FOLIO, GOVERNMENTLEGAL (216x330 mm ; 8.50x13.00 in)' => 'FOLIO',
			'EXECUTIVE, MONARCH (184x267 mm ; 7.25x10.50 in)' => 'EXECUTIVE',
			'MEMO, STATEMENT, ORGANIZERL (140x216 mm ; 5.50x8.50 in)' => 'MEMO',
			'FOOLSCAP (210x330 mm ; 8.27x13.00 in)' => 'FOOLSCAP',
			'COMPACT (108x171 mm ; 4.25x6.75 in)' => 'COMPACT',
			'ORGANIZERJ (70x127 mm ; 2.75x5.00 in)' => 'ORGANIZERJ',
			'Canadian standard CAN 2-9.60M (default: P4)' => 'P4',
			'P1 (560x860 mm ; 22.05x33.86 in)' => 'P1',
			'P2 (430x560 mm ; 16.93x22.05 in)' => 'P2',
			'P3 (280x430 mm ; 11.02x16.93 in)' => 'P3',
			'P4 (215x280 mm ; 8.46x11.02 in)' => 'P4',
			'P5 (140x215 mm ; 5.51x8.46 in)' => 'P5',
			'P6 (107x140 mm ; 4.21x5.51 in)' => 'P6',
			'North American Architectural Sizes (default: ARCH_A)' => 'ARCH_A',
			'ARCH_E (914x1219 mm ; 36.00x48.00 in)' => 'ARCH_E',
			'ARCH_E1 (762x1067 mm ; 30.00x42.00 in)' => 'ARCH_E1',
			'ARCH_D (610x914 mm ; 24.00x36.00 in)' => 'ARCH_D',
			'ARCH_C, BROADSHEET (457x610 mm ; 18.00x24.00 in)' => 'ARCH_C',
			'ARCH_B (305x457 mm ; 12.00x18.00 in)' => 'ARCH_B',
			'ARCH_A (229x305 mm ; 9.00x12.00 in)' => 'ARCH_A',
			'Announcement Envelopes (default: ANNENV_A2)' => 'ANNENV_A2',
			'ANNENV_A2 (111x146 mm ; 4.37x5.75 in)' => 'ANNENV_A2',
			'ANNENV_A6 (121x165 mm ; 4.75x6.50 in)' => 'ANNENV_A6',
			'ANNENV_A7 (133x184 mm ; 5.25x7.25 in)' => 'ANNENV_A7',
			'ANNENV_A8 (140x206 mm ; 5.50x8.12 in)' => 'ANNENV_A8',
			'ANNENV_A10 (159x244 mm ; 6.25x9.62 in)' => 'ANNENV_A10',
			'ANNENV_SLIM (98x225 mm ; 3.87x8.87 in)' => 'ANNENV_SLIM',
			'Commercial Envelopes (default: COMMENV_N10)' => 'COMMENV_N10',
			'COMMENV_N6_1/4 (89x152 mm ; 3.50x6.00 in)' => 'COMMENV_N6_1/4',
			'COMMENV_N6_3/4 (92x165 mm ; 3.62x6.50 in)' => 'COMMENV_N6_3/4',
			'COMMENV_N8 (98x191 mm ; 3.87x7.50 in)' => 'COMMENV_N8',
			'COMMENV_N9 (98x225 mm ; 3.87x8.87 in)' => 'COMMENV_N9',
			'COMMENV_N10 (105x241 mm ; 4.12x9.50 in)' => 'COMMENV_N10',
			'COMMENV_N11 (114x263 mm ; 4.50x10.37 in)' => 'COMMENV_N11',
			'COMMENV_N12 (121x279 mm ; 4.75x11.00 in)' => 'COMMENV_N12',
			'COMMENV_N14 (127x292 mm ; 5.00x11.50 in)' => 'COMMENV_N14',
			'Catalogue Envelopes (default: CATENV_N10_1/2)' => 'CATENV_N10_1/2',
			'CATENV_N1 (152x229 mm ; 6.00x9.00 in)' => 'CATENV_N1',
			'CATENV_N1_3/4 (165x241 mm ; 6.50x9.50 in)' => 'CATENV_N1_3/4',
			'CATENV_N2 (165x254 mm ; 6.50x10.00 in)' => 'CATENV_N2',
			'CATENV_N3 (178x254 mm ; 7.00x10.00 in)' => 'CATENV_N3',
			'CATENV_N6 (191x267 mm ; 7.50x10.50 in)' => 'CATENV_N6',
			'CATENV_N7 (203x279 mm ; 8.00x11.00 in)' => 'CATENV_N7',
			'CATENV_N8 (210x286 mm ; 8.25x11.25 in)' => 'CATENV_N8',
			'CATENV_N9_1/2 (216x267 mm ; 8.50x10.50 in)' => 'CATENV_N9_1/2',
			'CATENV_N9_3/4 (222x286 mm ; 8.75x11.25 in)' => 'CATENV_N9_3/4',
			'CATENV_N10_1/2 (229x305 mm ; 9.00x12.00 in)' => 'CATENV_N10_1/2',
			'CATENV_N12_1/2 (241x318 mm ; 9.50x12.50 in)' => 'CATENV_N12_1/2',
			'CATENV_N13_1/2 (254x330 mm ; 10.00x13.00 in)' => 'CATENV_N13_1/2',
			'CATENV_N14_1/4 (286x311 mm ; 11.25x12.25 in)' => 'CATENV_N14_1/4',
			'CATENV_N14_1/2 (292x368 mm ; 11.50x14.50 in)' => 'CATENV_N14_1/2',
			'Japanese (JIS P 0138-61) Standard B-Series (default: JIS_B5)' => 'JIS_B5',
			'JIS_B0 (1030x1456 mm ; 40.55x57.32 in)' => 'JIS_B0',
			'JIS_B1 (728x1030 mm ; 28.66x40.55 in)' => 'JIS_B1',
			'JIS_B2 (515x728 mm ; 20.28x28.66 in)' => 'JIS_B2',
			'JIS_B3 (364x515 mm ; 14.33x20.28 in)' => 'JIS_B3',
			'JIS_B4 (257x364 mm ; 10.12x14.33 in)' => 'JIS_B4',
			'JIS_B5 (182x257 mm ; 7.17x10.12 in)' => 'JIS_B5',
			'JIS_B6 (128x182 mm ; 5.04x7.17 in)' => 'JIS_B6',
			'JIS_B7 (91x128 mm ; 3.58x5.04 in)' => 'JIS_B7',
			'JIS_B8 (64x91 mm ; 2.52x3.58 in)' => 'JIS_B8',
			'JIS_B9 (45x64 mm ; 1.77x2.52 in)' => 'JIS_B9',
			'JIS_B10 (32x45 mm ; 1.26x1.77 in)' => 'JIS_B10',
			'JIS_B11 (22x32 mm ; 0.87x1.26 in)' => 'JIS_B11',
			'JIS_B12 (16x22 mm ; 0.63x0.87 in)' => 'JIS_B12',
			'PA Series (default: PA4)' => 'PA4',
			'PA0 (840x1120 mm ; 33.07x44.09 in)' => 'PA0',
			'PA1 (560x840 mm ; 22.05x33.07 in)' => 'PA1',
			'PA2 (420x560 mm ; 16.54x22.05 in)' => 'PA2',
			'PA3 (280x420 mm ; 11.02x16.54 in)' => 'PA3',
			'PA4 (210x280 mm ; 8.27x11.02 in)' => 'PA4',
			'PA5 (140x210 mm ; 5.51x8.27 in)' => 'PA5',
			'PA6 (105x140 mm ; 4.13x5.51 in)' => 'PA6',
			'PA7 (70x105 mm ; 2.76x4.13 in)' => 'PA7',
			'PA8 (52x70 mm ; 2.05x2.76 in)' => 'PA8',
			'PA9 (35x52 mm ; 1.38x2.05 in)' => 'PA9',
			'PA10 (26x35 mm ; 1.02x1.38 in)' => 'PA10',
			'Standard Photographic Print Sizes (default: 8R, 6P)' => '8R',
			'PASSPORT_PHOTO (35x45 mm ; 1.38x1.77 in)' => 'PASSPORT_PHOTO',
			'E (82x120 mm ; 3.25x4.72 in)' => 'E',
			'3R, L (89x127 mm ; 3.50x5.00 in)' => '3R',
			'4R, KG (102x152 mm ; 4.02x5.98 in)' => '4R',
			'4D (120x152 mm ; 4.72x5.98 in)' => '4D',
			'5R, 2L (127x178 mm ; 5.00x7.01 in)' => '5R',
			'6R, 8P (152x203 mm ; 5.98x7.99 in)' => '6R',
			'8R, 6P (203x254 mm ; 7.99x10.00 in)' => '8R',
			'S8R, 6PW (203x305 mm ; 7.99x12.01 in)' => 'S8R',
			'10R, 4P (254x305 mm ; 10.00x12.01 in)' => '10R',
			'S10R, 4PW (254x381 mm ; 10.00x15.00 in)' => 'S10R',
			'11R (279x356 mm ; 10.98x14.02 in)' => '11R',
			'S11R (279x432 mm ; 10.98x17.01 in)' => 'S11R',
			'12R (305x381 mm ; 12.01x15.00 in)' => '12R',
			'S12R (305x456 mm ; 12.01x17.95 in)' => 'S12R',
			'Common Newspaper Sizes (default: NEWSPAPER_TABLOID)' => 'NEWSPAPER_TABLOID',
			'NEWSPAPER_BROADSHEET (750x600 mm ; 29.53x23.62 in)' => 'NEWSPAPER_BROADSHEET',
			'NEWSPAPER_BERLINER (470x315 mm ; 18.50x12.40 in)' => 'NEWSPAPER_BERLINER',
			'NEWSPAPER_COMPACT, NEWSPAPER_TABLOID (430x280 mm ; 16.93x11.02 in)' => 'NEWSPAPER_TABLOID',
			'Business Cards (default: BUSINESS_CARD)' => 'BUSINESS_CARD',
			'CREDIT_CARD, BUSINESS_CARD, BUSINESS_CARD_ISO7810 (54x86 mm ; 2.13x3.37 in)' => 'BUSINESS_CARD',
			'BUSINESS_CARD_ISO216 (52x74 mm ; 2.05x2.91 in)' => 'BUSINESS_CARD_ISO216',
			'BUSINESS_CARD_IT, UK, FR, DE, ES (55x85 mm ; 2.17x3.35 in)' => 'BUSINESS_CARD_IT',
			'BUSINESS_CARD_US, CA (51x89 mm ; 2.01x3.50 in)' => 'BUSINESS_CARD_US',
			'BUSINESS_CARD_JP (55x91 mm ; 2.17x3.58 in)' => 'BUSINESS_CARD_JP',
			'BUSINESS_CARD_HK (54x90 mm ; 2.13x3.54 in)' => 'BUSINESS_CARD_HK',
			'BUSINESS_CARD_AU, DK, SE (55x90 mm ; 2.17x3.54 in)' => 'BUSINESS_CARD_AU',
			'BUSINESS_CARD_RU, CZ, FI, HU, IL (50x90 mm ; 1.97x3.54 in)' => 'BUSINESS_CARD_RU',
			'Billboards (default: 4SHEET)' => '4SHEET',
			'4SHEET (1016x1524 mm ; 40.00x60.00 in)' => '4SHEET',
			'6SHEET (1200x1800 mm ; 47.24x70.87 in)' => '6SHEET',
			'12SHEET (3048x1524 mm ; 120.00x60.00 in)' => '12SHEET',
			'16SHEET (2032x3048 mm ; 80.00x120.00 in)' => '16SHEET',
			'32SHEET (4064x3048 mm ; 160.00x120.00 in)' => '32SHEET',
			'48SHEET (6096x3048 mm ; 240.00x120.00 in)' => '48SHEET',
			'64SHEET (8128x3048 mm ; 320.00x120.00 in)' => '64SHEET',
			'96SHEET (12192x3048 mm ; 480.00x120.00 in)' => '96SHEET',
			'Old Imperial English (default: EN_ATLAS)' => 'EN_ATLAS',
			'EN_EMPEROR (1219x1829 mm ; 48.00x72.00 in)' => 'EN_EMPEROR',
			'EN_ANTIQUARIAN (787x1346 mm ; 31.00x53.00 in)' => 'EN_ANTIQUARIAN',
			'EN_GRAND_EAGLE (730x1067 mm ; 28.75x42.00 in)' => 'EN_GRAND_EAGLE',
			'EN_DOUBLE_ELEPHANT (679x1016 mm ; 26.75x40.00 in)' => 'EN_DOUBLE_ELEPHANT',
			'EN_ATLAS (660x864 mm ; 26.00x34.00 in)' => 'EN_ATLAS',
			'EN_COLOMBIER (597x876 mm ; 23.50x34.50 in)' => 'EN_COLOMBIER',
			'EN_ELEPHANT (584x711 mm ; 23.00x28.00 in)' => 'EN_ELEPHANT',
			'EN_DOUBLE_DEMY (572x902 mm ; 22.50x35.50 in)' => 'EN_DOUBLE_DEMY',
			'EN_IMPERIAL (559x762 mm ; 22.00x30.00 in)' => 'EN_IMPERIAL',
			'EN_PRINCESS (546x711 mm ; 21.50x28.00 in)' => 'EN_PRINCESS',
			'EN_CARTRIDGE (533x660 mm ; 21.00x26.00 in)' => 'EN_CARTRIDGE',
			'EN_DOUBLE_LARGE_POST (533x838 mm ; 21.00x33.00 in)' => 'EN_DOUBLE_LARGE_POST',
			'EN_ROYAL (508x635 mm ; 20.00x25.00 in)' => 'EN_ROYAL',
			'EN_SHEET, EN_HALF_POST (495x597 mm ; 19.50x23.50 in)' => 'EN_SHEET, EN_HALF_POST',
			'EN_SUPER_ROYAL (483x686 mm ; 19.00x27.00 in)' => 'EN_SUPER_ROYAL',
			'EN_DOUBLE_POST (483x775 mm ; 19.00x30.50 in)' => 'EN_DOUBLE_POST',
			'EN_MEDIUM (445x584 mm ; 17.50x23.00 in)' => 'EN_MEDIUM',
			'EN_DEMY (445x572 mm ; 17.50x22.50 in)' => 'EN_DEMY',
			'EN_LARGE_POST (419x533 mm ; 16.50x21.00 in)' => 'EN_LARGE_POST',
			'EN_COPY_DRAUGHT (406x508 mm ; 16.00x20.00 in)' => 'EN_COPY_DRAUGHT',
			'EN_POST (394x489 mm ; 15.50x19.25 in)' => 'EN_POST',
			'EN_CROWN (381x508 mm ; 15.00x20.00 in)' => 'EN_CROWN',
			'EN_PINCHED_POST (375x470 mm ; 14.75x18.50 in)' => 'EN_PINCHED_POST',
			'EN_BRIEF (343x406 mm ; 13.50x16.00 in)' => 'EN_BRIEF',
			'EN_FOOLSCAP (343x432 mm ; 13.50x17.00 in)' => 'EN_FOOLSCAP',
			'EN_SMALL_FOOLSCAP (337x419 mm ; 13.25x16.50 in)' => 'EN_SMALL_FOOLSCAP',
			'EN_POTT (318x381 mm ; 12.50x15.00 in)' => 'EN_POTT',
			'Old Imperial Belgian (default: BE_ELEPHANT)' => 'BE_ELEPHANT',
			'BE_GRAND_AIGLE (700x1040 mm ; 27.56x40.94 in)' => 'BE_GRAND_AIGLE',
			'BE_COLOMBIER (620x850 mm ; 24.41x33.46 in)' => 'BE_COLOMBIER',
			'BE_DOUBLE_CARRE (620x920 mm ; 24.41x36.22 in)' => 'BE_DOUBLE_CARRE',
			'BE_ELEPHANT (616x770 mm ; 24.25x30.31 in)' => 'BE_ELEPHANT',
			'BE_PETIT_AIGLE (600x840 mm ; 23.62x33.07 in)' => 'BE_PETIT_AIGLE',
			'BE_GRAND_JESUS (550x730 mm ; 21.65x28.74 in)' => 'BE_GRAND_JESUS',
			'BE_JESUS (540x730 mm ; 21.26x28.74 in)' => 'BE_JESUS',
			'BE_RAISIN (500x650 mm ; 19.69x25.59 in)' => 'BE_RAISIN',
			'BE_GRAND_MEDIAN (460x605 mm ; 18.11x23.82 in)' => 'BE_GRAND_MEDIAN',
			'BE_DOUBLE_POSTE (435x565 mm ; 17.13x22.24 in)' => 'BE_DOUBLE_POSTE',
			'BE_COQUILLE (430x560 mm ; 16.93x22.05 in)' => 'BE_COQUILLE',
			'BE_PETIT_MEDIAN (415x530 mm ; 16.34x20.87 in)' => 'BE_PETIT_MEDIAN',
			'BE_RUCHE (360x460 mm ; 14.17x18.11 in)' => 'BE_RUCHE',
			'BE_PROPATRIA (345x430 mm ; 13.58x16.93 in)' => 'BE_PROPATRIA',
			'BE_LYS (317x397 mm ; 12.48x15.63 in)' => 'BE_LYS',
			'BE_POT (307x384 mm ; 12.09x15.12 in)' => 'BE_POT',
			'BE_ROSETTE (270x347 mm ; 10.63x13.66 in)' => 'BE_ROSETTE',
			'Old Imperial French (default: FR_PETIT_AIGLE)' => 'FR_PETIT_AIGLE',
			'FR_UNIVERS (1000x1300 mm ; 39.37x51.18 in)' => 'FR_UNIVERS',
			'FR_DOUBLE_COLOMBIER (900x1260 mm ; 35.43x49.61 in)' => 'FR_DOUBLE_COLOMBIER',
			'FR_GRANDE_MONDE (900x1260 mm ; 35.43x49.61 in)' => 'FR_GRANDE_MONDE',
			'FR_DOUBLE_SOLEIL (800x1200 mm ; 31.50x47.24 in)' => 'FR_DOUBLE_SOLEIL',
			'FR_DOUBLE_JESUS (760x1120 mm ; 29.92x44.09 in)' => 'FR_DOUBLE_JESUS',
			'FR_GRAND_AIGLE (750x1060 mm ; 29.53x41.73 in)' => 'FR_GRAND_AIGLE',
			'FR_PETIT_AIGLE (700x940 mm ; 27.56x37.01 in)' => 'FR_PETIT_AIGLE',
			'FR_DOUBLE_RAISIN (650x1000 mm ; 25.59x39.37 in)' => 'FR_DOUBLE_RAISIN',
			'FR_JOURNAL (650x940 mm ; 25.59x37.01 in)' => 'FR_JOURNAL',
			'FR_COLOMBIER_AFFICHE (630x900 mm ; 24.80x35.43 in)' => 'FR_COLOMBIER_AFFICHE',
			'FR_DOUBLE_CAVALIER (620x920 mm ; 24.41x36.22 in)' => 'FR_DOUBLE_CAVALIER',
			'FR_CLOCHE (600x800 mm ; 23.62x31.50 in)' => 'FR_CLOCHE',
			'FR_SOLEIL (600x800 mm ; 23.62x31.50 in)' => 'FR_SOLEIL',
			'FR_DOUBLE_CARRE (560x900 mm ; 22.05x35.43 in)' => 'FR_DOUBLE_CARRE',
			'FR_DOUBLE_COQUILLE (560x880 mm ; 22.05x34.65 in)' => 'FR_DOUBLE_COQUILLE',
			'FR_JESUS (560x760 mm ; 22.05x29.92 in)' => 'FR_JESUS',
			'FR_RAISIN (500x650 mm ; 19.69x25.59 in)' => 'FR_RAISIN',
			'FR_CAVALIER (460x620 mm ; 18.11x24.41 in)' => 'FR_CAVALIER',
			'FR_DOUBLE_COURONNE (460x720 mm ; 18.11x28.35 in)' => 'FR_DOUBLE_COURONNE',
			'FR_CARRE (450x560 mm ; 17.72x22.05 in)' => 'FR_CARRE',
			'FR_COQUILLE (440x560 mm ; 17.32x22.05 in)' => 'FR_COQUILLE',
			'FR_DOUBLE_TELLIERE (440x680 mm ; 17.32x26.77 in)' => 'FR_DOUBLE_TELLIERE',
			'FR_DOUBLE_CLOCHE (400x600 mm ; 15.75x23.62 in)' => 'FR_DOUBLE_CLOCHE',
			'FR_DOUBLE_POT (400x620 mm ; 15.75x24.41 in)' => 'FR_DOUBLE_POT',
			'FR_ECU (400x520 mm ; 15.75x20.47 in)' => 'FR_ECU',
			'FR_COURONNE (360x460 mm ; 14.17x18.11 in)' => 'FR_COURONNE',
			'FR_TELLIERE (340x440 mm ; 13.39x17.32 in)' => 'FR_TELLIERE',
			'FR_POT (310x400 mm ; 12.20x15.75 in)' => 'FR_POT' 
	);
	?>
	<!-- display general options -->
			<table>
				
				<tr>
					<td class="tr1"><?php _e('Display Option', 'wp-advanced-pdf')?></td>
					<td class="tr2"><input name="<?=PTPDF_PREFIX?>[front_end]" value="1"
						<?= ( isset( $ptpdfoptions['front_end'] ) ) ? 'checked="checked"' : ''; ?>
						type="checkbox" onclick="showHideCheck('frontsetting', this);" />
						<span><?php _e('Front End', 'wp-advanced-pdf')?></span> &nbsp; &nbsp; <input
						name="<?=PTPDF_PREFIX?>[admin_panel]" value="1"
						<?= ( isset( $ptpdfoptions['admin_panel'] ) ) ? 'checked="checked"' : ''; ?>
						type="checkbox" /> <span><?php _e('Admin Panel', 'wp-advanced-pdf')?></span></td>
				</tr>
				<tr id="frontsetting"
					class="<?= isset($ptpdfoptions['front_end'])  ? '' : 'noDis' ?>">
					<td class="tr1"><?php _e('Availability', 'wp-advanced-pdf')?></td>
					<td class="tr2">
					<select name="<?=PTPDF_PREFIX?>[availability]">
							<?php 
							ptpdf_profile_option( $ptpdfoptions['availability'], 'public', __('For all visitors', 'wp-advanced-pdf') );
							ptpdf_profile_option( $ptpdfoptions['availability'], 'private', __('Only for logged in users', 'wp-advanced-pdf') );
							?>
					</select></td>
				</tr>
			</table>
			<h3><?php _e('General', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<table>

					<tr>
					<td class="tr1"><?php _e('Allowed Post Types', 'wp-advanced-pdf')?></td>
						<td class="tr2">
							<?php
					$post_types = get_post_types( array( 'public'   => true ), 'names' );
					foreach ( $post_types as $post_type ) { ?>
                    <input name="<?=PTPDF_PREFIX?>[<?= $post_type; ?>]"
                           value="1" <?= ( isset( $ptpdfoptions[$post_type] )  ? 'checked="checked"' : ''); ?>
                           type="checkbox"/> <?= $post_type; ?><br/>
                <?php } ?>
                <p><?php _e('Select Post type for which You want to export PDF.', 'wp-advanced-pdf');?></p>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Include Cache', 'wp-advanced-pdf');?></td>
						<td class='tr2'><input name="<?=PTPDF_PREFIX?>[includefromCache]" value="1"
							<?= (isset( $ptpdfoptions['includefromCache'] ) ? 'checked="checked"' : ''); ?>
							type="checkbox" /> <div class="descr"><?php _e('By default PDF will be generated on fly. Select to generate PDF from cache.', 'wp-advanced-pdf');?></div>
							</td>
					</tr>
					<tr>
					<td class="tr1"><?php _e('Schedule Cache Updation', 'wp-advanced-pdf');?></td>
					<td class='tr2'>
					<select name="<?=PTPDF_PREFIX?>[cache_updation_sch]">
					<?php 
					ptpdf_profile_option( $ptpdfoptions['cache_updation_sch'], 'none', __('None', 'wp-advanced-pdf') );
					ptpdf_profile_option( $ptpdfoptions['cache_updation_sch'], '86400', __('Daily', 'wp-advanced-pdf') );
					ptpdf_profile_option( $ptpdfoptions['cache_updation_sch'], '604800', __('Weekly', 'wp-advanced-pdf') );
					
					?>
					</select>
					</td>
					</tr>
					<tr>
					<td class="tr1"><?php _e('Default File Name', 'wp-advanced-pdf');?></td>
					<td class="tr2"><?php 
						echo '<select name="'.PTPDF_PREFIX.'[ced_file_name]">';
						ptpdf_profile_option( $ptpdfoptions['ced_file_name'], 'postID', __('Post ID', 'wp-advanced-pdf') );
						ptpdf_profile_option( $ptpdfoptions['ced_file_name'], 'post_name', __('Post Name', 'wp-advanced-pdf') );
						?>
					</td>
					</tr>
					
			        <?php $author = array( 'None' => '', 'First Name' => 'first_name', 'Last Name' => 'last_name', 'Nickname' => 'nickname' ); ?>
			        <tr>
						<td class='tr1'><?php _e('Display Author Detail', 'wp-advanced-pdf');?></td>
						<td class='tr2'>
	                <?php
						echo '<select name="'.PTPDF_PREFIX.'[authorDetail]">';
						foreach ( $author as $key => $value ) {
							if ($ptpdfoptions ['authorDetail'] == '') {
								'selected="None"';
								$checked = ($ptpdfoptions ['authorDetail'] == $value) ? 'selected="selected"' : '';
								echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
							} else {
								if ($ptpdfoptions ['authorDetail']) {
									$checked = ($ptpdfoptions ['authorDetail'] == $value) ? 'selected="selected"' : '';
								}
								echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
							}
						}
						echo '</select>';
																	?>
	                    <div class="descr"><?php _e("Select if you would like to include the author name in the PDF, and how it should be formatted.", 'wp-advanced-pdf');?></div>
						</td>
					</tr>
					<tr>
						<td class='tr1'><?php _e('Display Post Category List', 'wp-advanced-pdf');?></td>
						<td class='tr2'><input name="<?=PTPDF_PREFIX?>[postCategories]" value="1"
							<?= ( isset( $ptpdfoptions['postCategories'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox" />
							<div class="descr"><?php _e('Select if you would like to include the post category list in the PDF.', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr>
						<td class='tr1'><?php _e('Display Post Tag List', 'wp-advanced-pdf');?></td>
						<td class='tr2'><input name="<?=PTPDF_PREFIX?>[postTags]" value="1"
							<?= ( isset( $ptpdfoptions['postTags'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox" />
							<div class="descr"><?php _e('Select if you would like to include the post
								tag list in the PDF.', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr>
						<td class='tr1'><?php _e('Display Post Date', 'wp-advanced-pdf');?></td>
						<td class='tr2'><input name="<?=PTPDF_PREFIX?>[postDate]" value="1"
							<?= ( isset( $ptpdfoptions['postDate'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox" />
							<div class="descr"><?php _e('Select if you would like to include the post
								date in the PDF.', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr>
						<td class='tr1'><?php _e('Send mail on publish', 'wp-advanced-pdf');?></td>
						<td class='tr2'><input name="<?=PTPDF_PREFIX?>[postPublishs]" value="1"
							<?= ( isset( $ptpdfoptions['postPublishs'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox" id="ptpdfcheck" />
							<div class="descr"><?php _e('Select if you would like to send PDF through mail on post publish.', 'wp-advanced-pdf');?></div></td>
						<td id="hide1">
							<select id="select_user_type" name="<?=PTPDF_PREFIX?>[postPublish][]" multiple="multiple"><?php wp_dropdown_roles(); ?>
							</select>
							<div class="descr">
								<?php 	if( isset( $ptpdfoptions['postPublish'] ) ) {
											foreach ( $ptpdfoptions['postPublish'] as $key => $value) {
												$length = count( $ptpdfoptions[ 'postPublish' ] ) - 1;
												echo ucfirst( $value );
												if( $length>$key )
												{
													echo ' ' . ',' . ' ' ;
												}	
											} 
										} ?>
							</div>
						</td>	
					</tr>
				</table>
			</div>
			<!-- section-2 Button positioning -->
			<h3><?php _e( 'Button Positioning', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<table>
					<tr>
						<td class="tr1"><?php _e( 'Alignment', 'wp-advanced-pdf')?></td>
						<td class="tr2"><select id="<?=PTPDF_PREFIX?>_content_position"
							name="<?=PTPDF_PREFIX?>[content_position]">
								<?php 
								ptpdf_profile_option( $ptpdfoptions['content_position'], 'left', __('Left Align', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['content_position'], 'right', __('Right Align', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['content_position'], 'center', __('Center', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['content_position'], 'none', __('None', 'wp-advanced-pdf') );
								?>
						</select></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e( 'Select Placement', 'wp-advanced-pdf')?></td>
						<td class="tr2"><select id="pf_content_placement"
							name="<?=PTPDF_PREFIX?>[content_placement]">
								<?php 
								ptpdf_profile_option( $ptpdfoptions['content_placement'], 'before', __('Above Content', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['content_placement'], 'after', __('Below Content', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['content_placement'], 'beforeandafter', __('Below and Above Content', 'wp-advanced-pdf') );
								?>
						</select>
							<div class="descr"><?php _e('Select where to place the PDF icon (before or
								after content; left or right aligned).', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e( 'Select Button', 'wp-advanced-pdf')?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[link_button]"
							id="link_button">
								<?php 
								ptpdf_profile_option( $ptpdfoptions['link_button'], 'default', __('Default', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['link_button'], 'custom', __('Custom', 'wp-advanced-pdf') );
								?>

						</select><br>
							<button type="button" class="upload_link_button button"><?php _e( 'Upload/Add image', 'wp-advanced-pdf'); ?></button>
						</td>
						<td id="custom_link">
							<div id="customlink" style="float: left; margin-right: 10px;">
								<img id="imglink"
									src="<?= ( isset($ptpdfoptions['custon_link_url'] )) ? $ptpdfoptions['custon_link_url'] :''; ?>"
									width="120px" height="120px" />
							</div>
							<div id="remove_link_button" style="line-height: 60px; float: right;">
								<input type="hidden" id="custon_link_url"
									name="<?=PTPDF_PREFIX?>[custon_link_url]"
									value="<?= (isset( $ptpdfoptions['custon_link_url'] )) ? $ptpdfoptions['custon_link_url'] : ''; ?>" />

								<button type="button" class="remove_link_button button"><?php _e( 'Remove image', 'wp-advanced-pdf'); ?></button>
							</div> 

						</td>
					</tr>
				</table>
			</div>
			<h3><?php _e('Body', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<table>
					<tr>
						<td class="tr1"><?php _e('Display Featured Image', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input name='<?=PTPDF_PREFIX?>[show_feachered_image]'
							value='1'
							<?= ( isset($ptpdfoptions['show_feachered_image'])) ? 'checked = "checked"' : ''?>
							type='checkbox' />
							<div class="descr"><?php _e('If a featured image has been set for the
								particular post/page,<br/> it will be displayed just below the title.', 'wp-advanced-pdf');?></div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Image Scaling Ratio', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[imageScale]"
							id="<?=PTPDF_PREFIX?>[imageScale]"
							value="<?= ( isset( $ptpdfoptions['imageScale'] ) ? $ptpdfoptions['imageScale'] : '1.25' );?>" />
							<div class="descr"><?php _e('
								Enter your desired image scaling ratio as a decimal (default is
								1.25). This represents <br> the relative size of the image in
								the browser vs the size of the image in the PDF.<br> Thus, 1.25
								yields a 1.25:1 scale of web:PDF.', 'wp-advanced-pdf');?>
							</div></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Top Margin', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[marginTop]"
							id="<?=PTPDF_PREFIX?>[marginTop]"
							value="<?= ( isset( $ptpdfoptions['marginTop'] ) ? $ptpdfoptions['marginTop'] : '27'); ?>" />
							<div class="descr"><?php _e('Enter your desired top margin (default is 27', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Left Margin', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[marginLeft]"
							id="<?=PTPDF_PREFIX?>[marginLeft]"
							value="<?= ( isset( $ptpdfoptions['marginLeft'] ) ? $ptpdfoptions['marginLeft'] : '15'); ?>" />
							<div class="descr"><?php _e('Enter your desired left margin (default is 15', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Right Margin', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[marginRight]"
							id="<?=PTPDF_PREFIX?>[marginRight]"
							value="<?= ( isset( $ptpdfoptions['marginRight'] ) ? $ptpdfoptions['marginRight'] : '15'); ?>" />
							<div class="descr"><?php _e('Enter your desired right margin (default is 15', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Page Size', 'wp-advanced-pdf');?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[page_size]">
							<?php foreach ( $page_size as $key => $value ) {
								ptpdf_profile_option( $ptpdfoptions['page_size'], $value, __($key, 'wp-advanced-pdf') );
							 }?>
							</select>

							<div class="descr"><?php _e('Select the desired page size (default is
								LETTER).', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Orientation', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input name="<?=PTPDF_PREFIX?>[page_orientation]" value="P"
							<?php if ( isset( $ptpdfoptions['page_orientation'] ) && 'P' == ( $ptpdfoptions['page_orientation'] ) ) echo 'checked="checked"'; ?>
							type="radio" /> Portrait&nbsp;&nbsp;&nbsp; <input
							name="<?=PTPDF_PREFIX?>[page_orientation]" value="L"
							<?php if ( isset( $ptpdfoptions['page_orientation'] ) && 'L' == ( $ptpdfoptions['page_orientation'] ) ) echo 'checked="checked"'; ?>
							type="radio" /> Landscape <br />
							<div class="descr"><?php _e('Select the desired page orientation (default
								is Portrait).', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Unit of Measurement', 'wp-advanced-pdf');?></td>
						<td class="tr2">
			                <?php
								$unit = array (
										'Point' => 'pt',
										'Millimeter' => 'mm',
										'Centimeter' => 'cm',
										'Inch' => 'in' 
								);
								echo '<select name="'.PTPDF_PREFIX.'[unitmeasure]">';
								foreach ( $unit as $key => $value ) {
									if ($ptpdfoptions ['unitmeasure'] == '') {
										'selected="Millimeter"';
										$checked = ($ptpdfoptions ['unitmeasure'] == $value) ? 'selected="selected"' : '';
										echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
									} else {
										if ($ptpdfoptions ['unitmeasure']) {
											$checked = ($ptpdfoptions ['unitmeasure'] == $value) ? 'selected="selected"' : '';
										}
										echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
									}
								}
								echo '</select>';
								?>
			                <div class="descr"><?php _e('Select the desired unit of
								measurement (default is mm).', 'wp-advanced-pdf');?></div>
						</td>
					</tr>
					
					<tr>
						<td class="tr1"><?php _e('Content Font', 'wp-advanced-pdf');?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[content_font_pdf]"
							id="content_font_pdf">
							<?php foreach ( $fonts as $key => $value ) {
								ptpdf_profile_option( $ptpdfoptions['content_font_pdf'], $value, __($key, 'wp-advanced-pdf') );
							}?>
						</select></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Content Font Size', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[content_font_size]"
							value="<?= ( isset ( $ptpdfoptions['content_font_size'] ) ? $ptpdfoptions['content_font_size'] : '10' ); ?>" />

						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('RTL Support', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input name="<?=PTPDF_PREFIX?>[rtl_support]" value="1"
							<?= ( isset ( $ptpdfoptions['rtl_support'] ) ? 'checked="checked"' : '' ); ?>
							type="checkbox" />
							<div class="descr"><?php _e('Select if you want to generate pdf for Post in Persian and Arabic language', 'wp-advanced-pdf');?></div>
							</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Custom Title', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[custom_title]"
							value="<?= ( isset($ptpdfoptions['custom_title'] ) ? $ptpdfoptions['custom_title'] : ''); ?>" />
							<div class="descr"><?php _e('Enter custom title to replace with post title.', 'wp-advanced-pdf');?></div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Custom CSS', 'wp-advanced-pdf');?></td>
						<td class="tr1"><input name="<?=PTPDF_PREFIX?>[CustomCSS_option]" value="1"
							<?= ( isset( $ptpdfoptions['CustomCSS_option'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox" onclick="showHideCheck('docCustomCSS', this);" />
							<div class="descr"><?php _e('Select if you would like to apply a custom css
								to all PDFs generated.', 'wp-advanced-pdf');?></div></td>
					</tr>

				</table>
				<table id="docCustomCSS"
					class="<?= isset($ptpdfoptions['CustomCSS_option'])  ? '' : 'noDis' ?>">
					<tr>
						<td class="tr1"></td>
						<td class="tr2"><textarea name="<?=PTPDF_PREFIX?>[Customcss]"
								class="cusDocEntryTpl"><?= ( isset($ptpdfoptions['Customcss'])) ? $ptpdfoptions['Customcss'] : '' ?></textarea></td>
					</tr>
				</table>

			</div>
			<h3><?php _e('Header', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<table>
					<tr>
						<td class="tr1"><?php _e('Page Header', 'wp-advanced-pdf');?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[page_header]" id="page_header">
								<?php 
								ptpdf_profile_option( $ptpdfoptions['page_header'], 'None', __('None', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['page_header'], 'upload-image', __('Upload an Image', 'wp-advanced-pdf') );
								?>
						</select><br>
							<button type="button" class="upload_imglogo_button button"><?php _e( 'Upload/Add image', 'wp-advanced-pdf'); ?></button>
						</td>
						<td id="custom_logo">
							<div id="customlogo" style="float: left; margin-right: 10px;">
								<img id="imglogo"
									src="<?= ( !empty($ptpdfoptions['logo_img_url'] )) ? $ptpdfoptions['logo_img_url'] :''; ?>"
									width="120px" height="120px" />
							</div>
							<div style="line-height: 60px; float: right;">
								<input type="hidden" id="logo_img_url"
									name="<?=PTPDF_PREFIX?>[logo_img_url]"
									value="<?= (isset( $ptpdfoptions['logo_img_url'] )) ? $ptpdfoptions['logo_img_url'] : ''; ?>" />

								<button type="button" class="remove_logo_button button"><?php _e( 'Remove image', 'wp-advanced-pdf'); ?></button>
							</div> 

						</td>
					</tr>
					<tr class="show_site_desc">
						<td class="tr1"><?php _e('Show Site name ', 'wp-advanced-pdf');?></td>
						<td class="tr1" ><input name="<?=PTPDF_PREFIX?>[show_site_name]" value="1"
							<?= ( isset( $ptpdfoptions['show_site_name'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox"  />
							<div class="descr"><?php _e('Select if you would like to show site name along with logo.', 'wp-advanced-pdf');?></div></td>
					</tr>
					<tr class="show_site_desc">
						<td class="tr1"><?php _e('Show Site Description ', 'wp-advanced-pdf');?></td>
						<td class="tr1" ><input name="<?=PTPDF_PREFIX?>[show_site_descR]" value="1"
							<?= ( isset( $ptpdfoptions['show_site_descR'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox"  />
							<div class="descr"><?php _e('Select if you would like to show site description along with logo.', 'wp-advanced-pdf');?></div></td>
					
					</tr>
					<tr class="show_site_desc">
						<td class="tr1"><?php _e('Show Site URL ', 'wp-advanced-pdf');?></td>
						<td class="tr1" ><input name="<?=PTPDF_PREFIX?>[show_site_URL]" value="1"
							<?= ( isset( $ptpdfoptions['show_site_URL'] ) ) ? 'checked="checked"' : ''; ?>
							type="checkbox"  />
							<div class="descr"><?php _e('Select if you would like to show site URL along with logo.', 'wp-advanced-pdf');?></div></td>
					</tr>					
					<tr>
						<td class="tr1"><?php _e('Logo Image Factor', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[imagefactor]"
							id="<?=PTPDF_PREFIX?>[imagefactor]"
							value="<?= ( isset( $ptpdfoptions['imagefactor'] ) ? $ptpdfoptions['imagefactor'] : '25'); ?>" />
							<div class="descr"><?php _e('
								This will applied to logo width/height<br>to provide logo image
								some surrounding space.', 'wp-advanced-pdf');?>
							</div></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Logo Top Margin', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[logomTop]"
							id="<?=PTPDF_PREFIX?>[logomTop]"
							value="<?= ( isset($ptpdfoptions['logomTop'] ) ? $ptpdfoptions['logomTop'] : '10'); ?>" />
							<div class="descr"><?php _e('Enter your desired top margin (default is 10', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Header Font', 'wp-advanced-pdf');?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[header_font_pdf]"
							id="header_font_pdf">
							<?php foreach ( $fonts as $key => $value ) {
								ptpdf_profile_option( $ptpdfoptions['header_font_pdf'], $value, __($key, 'wp-advanced-pdf') );
							}?>
						</select></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Header Font Size', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[header_font_size]"
							value="<?= ( isset( $ptpdfoptions['header_font_size'] ) ? $ptpdfoptions['header_font_size'] : '10'); ?>" />

						</td>
					</tr>

				</table>
			</div>
			<!-- Custom footer --->
			<h3><?php _e('Footer', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<table>
					<tr>
						<td class="tr1"><?php _e('Custom Footer Text', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="checkbox"
							name="<?=PTPDF_PREFIX?>[custom_footer_option]" value='1'
							<?= ( isset($ptpdfoptions['custom_footer_option'])) ? 'checked = "checked"' : ''?>
							onclick="showHideCheck('custom_footer_text', this);" /></td>
					</tr>
					<tr id="custom_footer_text"
						class="<?= isset($ptpdfoptions['custom_footer_option'])  ? '' : 'noDis' ?>">

						<td class="tr1"></td>
						<td class="tr2"><textarea id='custom_footer'
								name="<?=PTPDF_PREFIX?>[custom_footer]" class="cusDocEntryTpl"><?= ( isset($ptpdfoptions['custom_footer']) ? $ptpdfoptions['custom_footer'] : '') ?></textarea></td>
					</tr>


					<tr>
						<td class="tr1"><?php _e('Footer Font', 'wp-advanced-pdf');?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[footer_font_pdf]"
							id="footer_font_pdf">
							<?php foreach ( $fonts as $key => $value ) {
								ptpdf_profile_option( $ptpdfoptions['footer_font_pdf'], $value, __($key, 'wp-advanced-pdf') );
							}?>
						</select></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Font Size', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[footer_font_size]"
							value="<?= ( isset ( $ptpdfoptions['footer_font_size'] ) ? $ptpdfoptions['footer_font_size'] : '10' ); ?>" />
							<div class="descr"><?php _e('Select desired footer font size(default is 10', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Cell Width', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text"
							name="<?=PTPDF_PREFIX?>[footer_cell_width]"
							value="<?= ( isset ( $ptpdfoptions['footer_cell_width'] ) ? $ptpdfoptions['footer_cell_width'] : '0' ); ?>" />
							<div class="descr"><?php _e('Select desired Footer Cell Width(default is 0', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Cell Minimum Height', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text"
							name="<?=PTPDF_PREFIX?>[footer_min_height]"
							value="<?= ( isset ( $ptpdfoptions['footer_min_height'] ) ? $ptpdfoptions['footer_min_height'] : '0' ); ?>" />
							<div class="descr"><?php _e('Select desired Footer Cell Minimum Height(default is 0', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Cell Upper Left Corner (X)', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[footer_lcornerX]"
							value="<?= ( isset ( $ptpdfoptions['footer_lcornerX'] ) ? $ptpdfoptions['footer_lcornerX'] : '15' ); ?>" />
							<div class="descr"><?php _e('Select desired Footer Cell Upper Left Corner (X)(default is 15', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Cell Upper Left Corner (Y)', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text"
							name="<?=PTPDF_PREFIX?>[footer_font_lcornerY]"
							value="<?= ( isset ( $ptpdfoptions['footer_font_lcornerY'] ) ? $ptpdfoptions['footer_font_lcornerY'] : '290' ); ?>" />
							<div class="descr"><?php _e('Select your desired Footer Cell Upper Left Corner (Y)(default is 290', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Margin', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text"
							name="<?=PTPDF_PREFIX?>[footer_font_margin]"
							value="<?= ( isset ( $ptpdfoptions['footer_font_margin'] ) ? $ptpdfoptions['footer_font_margin'] : '10' ); ?>" />
							<div class="descr"><?php _e('Select desired footer font margin(default is 10', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Cell Fill', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input name="<?=PTPDF_PREFIX?>[footer_cell_fill]" value="1"
							<?= ( isset ( $ptpdfoptions['footer_cell_fill'] ) ? 'checked="checked"' : '' ); ?>
							type="checkbox" />
							<div class="descr"><?php _e('Select if you like the footer cell to be painted (default is no).', 'wp-advanced-pdf');?></div>
							</td>
					</tr>
					<tr>
					<?php $footeralign = array( 'Auto' => '', 'Left' => 'L', 'Right' => 'R', 'Center' => 'C' ); ?>
						<td class="tr1"><?php _e('Footer Cell Text Alignment', 'wp-advanced-pdf')?></td>
						<td class="tr2"><select name="<?=PTPDF_PREFIX?>[footer_align]">
							<?php 	foreach ( $footeralign as $key => $value ) {
								ptpdf_profile_option( $ptpdfoptions['footer_align'], $value, __($key, 'wp-advanced-pdf') );
							}?></select>
							<div class="descr"><?php _e('Select your desired text alignment for the footer cell..', 'wp-advanced-pdf');?></div>
							</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Footer Cell Auto-padding', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input name="<?=PTPDF_PREFIX?>[footer_cell_auto_padding]"
							value="1"
							<?= (  isset ( $ptpdfoptions['footer_cell_auto_padding'] ) ? 'checked="checked"' : '' ); ?>
							type="checkbox" /></td>
					</tr>
				</table>
			</div>

			
			<!-- Add watermark to pdfs -->
			<h3><?php _e('Watermark', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<div class="descr"><?php _e('This options gives you the possibility to add
					watermark text to PDF pages.', 'wp-advanced-pdf');?></div>
				<table>
					<tr>
						<td class="tr1"><?php _e('Watermark Text', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="checkbox"
							name="<?=PTPDF_PREFIX?>[add_watermark]" value='1'
							<?= ( isset($ptpdfoptions['add_watermark'])) ? 'checked = "checked"' : ''?>
							onclick="showHideCheck('doc_add_watermark', this);" /></td>
					</tr>

				</table>
				<table id="doc_add_watermark"
					class="<?= isset($ptpdfoptions['add_watermark'])  ? '' : 'noDis' ?>">
					<tr>
						<td class="tr1"><?php _e('Watermark Font', 'wp-advanced-pdf');?></td>
						<td><select name="wppdf[water_font]"><?php
						foreach ( $fonts as $key => $value ) {
							ptpdf_profile_option( $ptpdfoptions['water_font'], $value, __($key, 'wp-advanced-pdf') );
						}
						?></select></td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Rotation', 'wp-advanced-pdf');?></td>
						<td><input type="text" name="<?=PTPDF_PREFIX?>[rotate_water]"
							value="<?= (isset ( $ptpdfoptions['rotate_water'] ) ? $ptpdfoptions['rotate_water'] : '45'); ?>" />
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Watermark Text:', 'wp-advanced-pdf');?></td>
						<td class="tr2"><textarea id='docEntryTpl'
								name="<?=PTPDF_PREFIX?>[watermark_text]" class="cusDocEntryTpl"><?= ( isset($ptpdfoptions['watermark_text'])) ? $ptpdfoptions['watermark_text'] : '' ?></textarea></td>
					</tr>
				</table>
				<!-- Image watermark -->
				<table>
					<tr>
						<td class="tr1"><?php _e('Image', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="checkbox"
							name="<?=PTPDF_PREFIX?>[add_watermark_image]" value='1'
							<?= ( isset($ptpdfoptions['add_watermark_image'])) ? 'checked = "checked"' : ''?>
							onclick="showHideCheck('doc_add_watermark_image', this);" /></td>
					</tr>
				</table>
				<table id="doc_add_watermark_image"
					class="<?= isset($ptpdfoptions['add_watermark_image'])  ? '' : 'noDis' ?>">
					<tr>
						<td class="tr1"><?php _e('Image Height', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[water_img_h]"
							value="<?= (isset ( $ptpdfoptions['water_img_h'] ) ? $ptpdfoptions['water_img_h'] : ''); ?>" />
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Image Weight', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[water_img_w]"
							value="<?= ( isset ( $ptpdfoptions['water_img_w'] ) ? $ptpdfoptions['water_img_w'] : ''); ?>" />
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Image Transparency', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text" name="<?=PTPDF_PREFIX?>[water_img_t]"
							value="<?= ( isset ( $ptpdfoptions['water_img_t'] ) ? $ptpdfoptions['water_img_t'] : '0.1'); ?>" />
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Watermark Image:', 'wp-advanced-pdf');?></td>
						<td>
						<?php $src_waterark= isset($ptpdfoptions['background_img_url'] ) ? $ptpdfoptions['background_img_url'] : '';?>
						<div id="Watermark_Background"
								style="float: left; margin-right: 10px;">
								<img id="wbgimg" src="<?= $src_waterark ?>" width="60px"
									height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="background_img_url"
									name="<?=PTPDF_PREFIX?>[background_img_url]"
									value="<?= $src_waterark?>" />
								<button type="button" class="upload_imgbg_button button"><?php _e( 'Upload/Add image', 'wp-advanced-pdf'); ?></button>
								<button type="button" class="remove_img_button button"><?php _e( 'Remove image', 'wp-advanced-pdf'); ?></button>
							</div> 
						</td>
					</tr>
				</table>
			</div>
			<!-- Custom bullet style -->
			<h3><?php _e('Custom Bullet Style', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<div class="descr"><?php _e('Set the default bullet to be used as LI bullet
					symbol.', 'wp-advanced-pdf');?></div>
				<table>
					<tr>
						<td class="tr1"><?php _e('Bullet Symbol:', 'wp-advanced-pdf');?></td>
						<td><div id="Watermark_bullet"
								style="float: left; margin-right: 10px;">
								<img id="bulletimg"
									src="<?= (isset($ptpdfoptions['bullet_img_url'] )) ? $ptpdfoptions['bullet_img_url'] :'' ?>"
									width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="bullet_img_url"
									name="<?=PTPDF_PREFIX?>[bullet_img_url]"
									value="<?= (isset($ptpdfoptions['bullet_img_url'] )) ? $ptpdfoptions['bullet_img_url'] : '';?>" />
								<button type="button" class="upload_bullet_button button"><?php _e( 'Upload/Add image', 'wp-advanced-pdf'); ?></button>
								<button type="button" class="remove_bullet_button button"><?php _e( 'Remove image', 'wp-advanced-pdf'); ?></button>
							</div> 
							</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Custom Bullet Image Height', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text"
							name="<?=PTPDF_PREFIX?>[custom_image_height]"
							value="<?= ( isset ($ptpdfoptions['custom_image_height'] ) ? $ptpdfoptions['custom_image_height'] : '3' ); ?>" />
							<div class="descr"><?php _e('Select custom Bullet Image Height (default 2 ', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					<tr>
						<td class="tr1"><?php _e('Custom Bullet Image Width', 'wp-advanced-pdf');?></td>
						<td class="tr2"><input type="text"
							name="<?=PTPDF_PREFIX?>[custom_image_width]"
							value="<?= ( isset ($ptpdfoptions['custom_image_width'] ) ? $ptpdfoptions['custom_image_width'] : '2' ); ?>" />
							<div class="descr"><?php _e('Select custom Bullet Image Width (default 3 ', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
				</table>
			</div>
			<!-- Advanced Setting -->
			<h3><?php _e('Advanced Setting', 'wp-advanced-pdf')?></h3>
			<div class="section_body">
				<div class="descr"><?php _e('Add Advanced Setting Options	.', 'wp-advanced-pdf');?></div>
				<table>
					<tr id="custom_font">
						<td class="tr1"><?php _e('Browse to Select Your Font in ', 'wp-advanced-pdf'); ?><strong><?php _e('.ttf format' ,'wp-advanced-pdf');?></strong></td>
						<td class="tr2" id="Add_custom_font">
							<input type="file" name="<?=PTPDF_PREFIX?>[custom_font_for_body]">
						</td>
						<td>
							<div class="inner">
								<p style="display: none" class="ptp-js-save-loader save-loader"><img src="<?php echo PTPDF_URL.'/asset/images/ajax-loader.gif';?>">Adding Now</p>
								<p style="display: none" class="ptp-js-save-status save-status">Font is Added</p>
							</div>
						</td>
					</tr>
					
					<tr>
						<td class="tr1">
							<?php _e('Set Page Rotation', 'wp-advanced-pdf')?>
						</td>
						<td>
							<select name="<?=PTPDF_PREFIX?>[set_rotation]">
								<?php 
								ptpdf_profile_option( $ptpdfoptions['set_rotation'], '0', __('No Rotation', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['set_rotation'], '90', __('90 degree', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['set_rotation'], '180', __('180 degree', 'wp-advanced-pdf') );
								ptpdf_profile_option( $ptpdfoptions['set_rotation'], '270', __('270 degree', 'wp-advanced-pdf') );
								?>
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="tr1">
							<?php _e('Set FontStretching', 'wp-advanced-pdf')?>
						</td>
						<td class="tr2">
							<input type="text" name="<?=PTPDF_PREFIX?>[fontStretching]" value="<?= ( isset ( $ptpdfoptions['fontStretching'] ) ? $ptpdfoptions['fontStretching'] : '100' ); ?>" />
							<div class="descr"><?php _e('Select desired font Stretching(default is 100', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					
					<tr>
						<td class="tr1">
							<?php _e('Set FontSpacing', 'wp-advanced-pdf');?>
						</td>
						<td class="tr2">
							<input type="text" name="<?=PTPDF_PREFIX?>[fontSpacig]" value="<?= ( isset ( $ptpdfoptions['fontSpacig'] ) ? $ptpdfoptions['fontSpacig'] : '0' ); ?>" />
							<div class="descr"><?php _e('Select desired font Spacing(default is 0', 'wp-advanced-pdf');?><?= ( isset( $ptpdfoptions['unitmeasure']) ? $ptpdfoptions['unitmeasure'] :'')?>).</div>
						</td>
					</tr>
					
					
				</table>
			</div>
			<div class="submit">
				<input type="submit" class="button-primary" name="<?=PTPDF_PREFIX?>[submit]" value="<?php _e( 'Save Changes', 'wp-advanced-pdf') ?>" />
			</div>
			
			
						

		
	</div>
</div>