// JavaScript Document
	function wpsu_go_premium(){
		alert(wpsu_obj.go_premium);
	}


	
	jQuery(document).ready(function($){


		function wpsu_submit_settings(){

			if($('.wpsu_temp_text').length>0){
				$('a.wpsu_ct span').html('Again ');
				$('a.wpsu_ci').show();

			}else{

				$('a.wpsu_ct span').html('');
				$('a.wpsu_ci').hide();
			}
		}

		wpsu_submit_settings();


		function wpsu_submit(data){

			var default_data = {
				action: 'wpsu_test_ajax',
				type: 'img',
			}

			$.extend(default_data, data);
			var loader = $('.wpsu .wpsu_loader');
			loader.show();

			$.get(ajaxurl, default_data, function (data ) {

				data = JSON.parse(data);

				$('.images_compression_report').html(data.compress_report);
				wpsu_submit_settings();
				loader.hide();
			});
		}


		$('.main .selection_css').click(function(){
			$('.selection_div, .wpsu_toggle').hide();
			$('.selection_div.css, .wpsu_toggle.css').show();
			//document.location.href = 'options-general.php?page=wp_su&type=css';
		});
		$('.main .selection_js').click(function(){
			$('.selection_div, .wpsu_toggle').hide();
			$('.selection_div.js, .wpsu_toggle.js').show();			
			//document.location.href = 'options-general.php?page=wp_su&type=js';
		});	
		
		$('.images_compression_report .op_linked').click(function(){
			document.location.href = 'options-general.php?page=wp_su&type=img&t='+wpsu_obj.wpsu_tab;
		});	
		
		$('body').on('click', '.images_compression_report ul li.wpsu_link_dir a.wpsu_ud', function(event){
			
			event.preventDefault();
			
			var linked = $(this).parent().attr('data-linked');
			if(wpsu_pro){

				// document.location.href = 'options-general.php?page=wp_su&type=img&t='+wpsu_obj.wpsu_tab+'&wpsu_link_dir='+linked;
				var data = {
					wpsu_link_dir:linked,
				}
				wpsu_submit(data);

			}else{
				wpsu_go_premium();
			}
			
		});			
	
		$('a.wpsu_ct').click(function(event){
			event.preventDefault();
			
			if(wpsu_pro){
				var ask = true;
				if($('.wpsu_temp_text').length>0){					
					ask = confirm("Are you sure you want to compress the already compressed images again?\n\n"+"This action is not reversible and you might will lose your original images.\n\n"+"It is recommended that you switch back all original directories and then compress or make a backup prior this action.");
				}
				if(ask){
					// document.location.href = 'options-general.php?page=wp_su&type=img&wpsu_ct&t='+wpsu_obj.wpsu_tab;
					var data = {
						wpsu_ct:'true',
					}
					wpsu_submit(data);

				}
			}else{
				wpsu_go_premium();
			}
			
		});	
		
		
		$('a.wpsu_ctr').click(function(event){	
			event.preventDefault();
			document.location.href = 'options-general.php?page=wp_su&type=img&t='+wpsu_obj.wpsu_tab;
			
		});		
		
			
		$('a.wpsu_ci').click(function(event){	
			event.preventDefault();
			var ask = confirm("Are you sure you want to delete all temp directories?\n\n"+$(this).attr('title'));
			if(ask){
				// document.location.href = 'options-general.php?page=wp_su&type=img&wpsu_clear_imgs&t='+wpsu_obj.wpsu_tab;

				var data = {
					wpsu_clear_imgs:'true',
				}
				wpsu_submit(data);


			}else{
				return false;
			}
			
		});	
		
		$('.settings .selection_js').click(function(){
			if($(this).hasClass('disabled')){
				$(this).removeClass('disabled');
				$('input[name="selection_js"]').val(1);
			}else{
				$(this).addClass('disabled');
				$('input[name="selection_js"]').val(0);
			}
		});	
		
		$('.settings .selection_css').click(function(){
			if($(this).hasClass('disabled')){
				$(this).removeClass('disabled');
				$('input[name="selection_css"]').val(1);
			}else{
				$(this).addClass('disabled');
				$('input[name="selection_css"]').val(0);
			}
		});		
		
		$('.wpsu_toggle').click(function(){
			$('.selection_div.sub').hide();
			$('.selection_div.main').show();
			$(this).hide();
		});
		
		$('.wpsu_modes').click(function(){
			var mode = $(this).attr('data-mode');
			$('.wpsu_todo_area').hide();
			$('.selection_div.main').hide();
			$('.wpsu_booster_area').hide();
			switch(mode){
				case "classic":					
					$('.selection_div.main').show();
				break;
				case "advanced":
					$('.wpsu_todo_area').show();									
				break;
				case "booster":
					$('.wpsu_booster_area').show();			
				break;				
			}
		});


		$('.wpsu a.nav-tab').click(function(){

			$(this).siblings().removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			// , form:not(.wrap.wc_settings_div .nav-tab-content)'
			$('.nav-tab-content').hide();
			$('.nav-tab-content').eq($(this).index()).show();
			window.history.replaceState('', '', wpsu_obj.this_url+'&t='+$(this).index());
			$('form input[name="wpsu_tn"]').val($(this).index());
			wpsu_obj.wpsu_tab_tab = $(this).index();
			wpsu_obj.wpsu_tab = $(this).index();

		});

	});