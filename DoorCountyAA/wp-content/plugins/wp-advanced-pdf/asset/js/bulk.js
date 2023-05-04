jQuery(document).ready(function($) {
	if ( $( '#select_user_type' ).length > 0 ) {
		jQuery('#select_user_type').select2({
			 placeholder: "Select user type",
			 allowClear: false
		});
	}

	if( $ ( '#ptpdfcheck' ).prop("checked") == true ) {
		jQuery( '#hide1' ).show();
	}
	else {
		jQuery( '#hide1' ).hide();
	}

	jQuery( '#ptpdfcheck' ).on( 'click', function() {
		if( $(this).prop("checked") == true ) {
			jQuery( '#hide1' ).show();
		}
		else {
			jQuery( '#hide1' ).hide();
		}
	});

	jQuery('#wpppdf_img_send_email').click(function(e) {
			e.preventDefault();
			jQuery(".wpppdf_img_email_image p").removeClass("ced_wpppdf_img_email_image_error");
			jQuery(".wpppdf_img_email_image p").removeClass("ced_wpppdf_img_email_image_success");

			jQuery(".wpppdf_img_email_image p").html("");
			var email = jQuery('.wpppdf_img_email_field').val();
			jQuery("#wpppdf_loader").removeClass("hide");
			jQuery("#wpppdf_loader").addClass("dislay");
			//alert(ajax_url);
			$.ajax({
		        type:'POST',
		        url :ajax_url,
		        data:{action:'wpppdf_send_mail',flag:true,emailid:email},
		        success:function(data)
		        {
					var new_data = JSON.parse(data);
					jQuery("#wpppdf_loader").removeClass("dislay");
					jQuery("#wpppdf_loader").addClass("hide");
					if(new_data['status']==true)
			        {		    	
						jQuery(".wpppdf_img_email_image p").addClass("ced_wpppdf_img_email_image_success");
						jQuery(".wpppdf_img_email_image p").html(new_data['msg']);
			        }
			        else
			        {
						jQuery(".wpppdf_img_email_image p").addClass("ced_wpppdf_img_email_image_error");
						jQuery(".wpppdf_img_email_image p").html(new_data['msg']);
			        }
		        }
	    	});
		});

	
	if(bulk_obj.post_export == true)
	{
		jQuery('<option>').val('export').text(bulk_obj.txt).appendTo("select[name='action']");
		jQuery('<option>').val('export').text(bulk_obj.txt).appendTo("select[name='action2']");
	}
});

jQuery(function ($) {
	// hides as soon as the DOM is ready
    jQuery('div.section_body').hide();
    // shows on clicking the noted link
    jQuery('#ptpdf-options').find('h3').click(function() {
        jQuery(this).toggleClass("open");
        jQuery(this).next("div").slideToggle('1000');
        $(".section_body").not($(this).next()).hide("slow","linear");
        jQuery('#ptpdf-options').find('h3').not($(this)).removeClass('open') ;
        return false;
    });
    jQuery('#page_header').bind('change', function() {
        if(jQuery(this).val() == 'None') {
          jQuery('#custom_logo').hide();
          //jQuery('.show_site_desc').hide();
          jQuery('.upload_imglogo_button').hide();
        } else {
        	if ( '' === jQuery( '#logo_img_url' ).val() ) {
        		jQuery( '#custom_logo' ).hide();
        	} else 
          jQuery('#custom_logo').show();
         // jQuery('.show_site_desc').show();
          jQuery('.upload_imglogo_button').show();
        }
      }).change();
  
    jQuery('#link_button').bind('change', function() {
        if(jQuery(this).val() == 'default') {
          jQuery('#custom_link').hide();
          jQuery('.upload_link_button').hide();
        } else {
        	if ( '' === jQuery( '#custon_link_url' ).val() ) {
        		jQuery( '#custom_link' ).hide();
        	} else
          jQuery('#custom_link').show();
          jQuery('.upload_link_button').show();
        }
      }).change();
    // Only show the "remove image" button when needed
	if ( '' === jQuery( '#custon_link_url' ).val() ) {
		jQuery( '#custom_link' ).hide();
	}
	if ( '' === jQuery( '#logo_img_url' ).val() ) {
		jQuery( '#custom_logo' ).hide();
	}
	if ( '' === jQuery( '#background_img_url' ).val() ) {
		jQuery( '#wbgimg' ).hide();
		jQuery( '.remove_img_button' ).hide();
	}
	if ( '' === jQuery( '#bullet_img_url' ).val() ) {
		jQuery( '.remove_bullet_button' ).hide();
		jQuery( '#bulletimg' ).hide();
	}
    
	$('#gentab').click(function(e) { switchTab(e, 'gentab'); });
		$('#advtab').click(function(e) { switchTab(e, 'advtab'); });
		function switchTab(e, tab) {
			e.preventDefault();
			
			// detect physical tab change to hide status messages
			if (typeof window.gdeTabChg != 'undefined') {
				$('#message').hide();
				delete window.gdeTabChg;
			}
			
			var newcontid = tab.substr(0,3) + "content";
			
			$('#' + newcontid).show();
			$('#' + tab).addClass('ui-tabs-selected ui-state-active');
			$('#' + tab).blur();
			
			if (tab !== 'gentab') {
				$('#gencontent').hide();
				$('#gentab').removeClass('ui-tabs-selected ui-state-active');
			}
			if (tab !== 'advtab') {
				$('#advcontent').hide();
				$('#advtab').removeClass('ui-tabs-selected ui-state-active');
			}
			
			
			// record tab change
			window.gdeTabChg = true;
		}
		
		
});
function showHideCheck(id,check) {
	if(check.checked) {
		document.getElementById(id).className = '';
	} else {
		document.getElementById(id).className = 'noDis';
	}
}
// only show the logo 
/*function status_to_show_hide_descriptions(id,check){
	var action_name = id;
	var data = new FormData();
	if(check.checked) {
		
		  data.append('action', action_name);
		  data.append('value', 'allowed');
			jQuery.ajax({
		    	url: ajaxurl,
		    	type: 'POST',
		    	data: data,
		        cache: false,
		        dataType: 'json',
		        processData: false, // Don't process the files
		        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		        success: function(data, textStatus, jqXHR) {
		        	//alert(data.data);
		        }
			});
	}else{
		
		  data.append('action', action_name);
		  data.append('value', 'not_allowed');
			jQuery.ajax({
		    	url: ajaxurl,
		    	type: 'POST',
		    	data: data,
		        cache: false,
		        dataType: 'json',
		        processData: false, // Don't process the files
		        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		        success: function(data, textStatus, jqXHR) {
		        	//alert('not');
		        }
			});
	}
	
	}*/


//Only show the "remove image" button when needed
if ( '' === jQuery( '#custon_link_url' ).val() ) {
	jQuery( '.remove_link_button' ).hide();
	jQuery( '#imglink' ).hide();
}

  jQuery(document).ready(function() {
        jQuery('.upload_link_button').click(function(){
             inputField = jQuery(this).parent('div');
             tb_show('watermark', 'media-upload.php?TB_iframe=true');
             window.send_to_editor = function(html)
             {  
            	url = jQuery(html).find('img').attr('src');
             	if(typeof url == 'undefined')
             		url = jQuery(html).attr('src');	
            	jQuery( '#custon_link_url' ).val( url );
            	jQuery( '#customlink' ).find( 'img' ).attr( 'src', url );
            	jQuery( '.remove_link_button' ).show();
            	jQuery( '#imglink' ).show();
            	jQuery('#custom_link').show();
                tb_remove();
             };
             return false;
		});
   });

jQuery( document ).on( 'click', '.remove_link_button', function() {
	jQuery('#custon_link_url').val('');
	jQuery('#imglink').hide();
	jQuery(this).hide();
	 
    return false;
});

//Only show the "remove image" button when needed
if ( '' === jQuery( '#logo_img_url' ).val() ) {
	jQuery( '.remove_logo_button' ).hide();
	jQuery( '#imglogo' ).hide();
}

  jQuery(document).ready(function() {
        jQuery('.upload_imglogo_button').click(function(){
             inputField = jQuery(this).parent('div');
             tb_show('Logo', 'media-upload.php?TB_iframe=true');
             window.send_to_editor = function(html)
             {  
            	url = jQuery(html).find('img').attr('src');
            	if(typeof url == 'undefined')
            		url = jQuery(html).attr('src');	
            	jQuery( '#logo_img_url' ).val( url );
            	jQuery( '#customlogo' ).find( 'img' ).attr( 'src', url );
            	jQuery( '.remove_logo_button' ).show();
            	jQuery( '#imglogo' ).show();
            	jQuery('#custom_logo').show();
                tb_remove();
             };
             return false;
		});
   });

jQuery( document ).on( 'click', '.remove_logo_button', function() {
	jQuery('#logo_img_url').val('');
	jQuery('#imglogo').hide();
	jQuery(this).hide();
	 
	  return false;
});



if ( '' === jQuery( '#background_img_url' ).val() ) {
	jQuery( '.remove_img_button' ).hide();
	jQuery( '#wbgimg' ).hide();
}

  jQuery(document).ready(function() {
        jQuery('.upload_imgbg_button').click(function(){
             inputField = jQuery(this).parent('div');
             tb_show('watermark', 'media-upload.php?TB_iframe=true');
             window.send_to_editor = function(html)
             {  
            	url = jQuery(html).find('img').attr('src');
             	if(typeof url == 'undefined')
             		url = jQuery(html).attr('src');	
            	jQuery( '#background_img_url' ).val( url );
            	jQuery( '#Watermark_Background' ).find( 'img' ).attr( 'src', url );
            	jQuery( '.remove_img_button' ).show();
            	jQuery( '#wbgimg' ).show();
                tb_remove();
             };
             return false;
		});
   });

jQuery( document ).on( 'click', '.remove_img_button', function() {
	jQuery('#background_img_url').val('');
	jQuery('#wbgimg').attr('src', '');
	jQuery(this).hide();
	jQuery( '#wbgimg' ).hide();
	  return false;
});


if ( '' === jQuery( '#bullet_img_url' ).val() ) {
	jQuery( '.remove_bullet_button' ).hide();
	jQuery( '#bulletimg' ).hide();
}

  jQuery(document).ready(function() {
        jQuery('.upload_bullet_button').click(function(){
             inputField = jQuery(this).parent('div');
             tb_show('Bullet Symbol', 'media-upload.php?TB_iframe=true');
             window.send_to_editor = function(html)
             {  
            	url = jQuery(html).find('img').attr('src');
             	if(typeof url == 'undefined')
             		url = jQuery(html).attr('src');	
            	jQuery( '#bullet_img_url' ).val( url );
            	jQuery( '#Watermark_bullet' ).find( 'img' ).attr( 'src', url );
            	jQuery( '.remove_bullet_button' ).show();
            	jQuery( '#bulletimg' ).show();
                tb_remove();
             };
             return false;
		});
   });

jQuery( document ).on( 'click', '.remove_bullet_button', function() {
	jQuery('#bullet_img_url').val('');
	jQuery('#bulletimg').attr('src', '');
	jQuery(this).hide();
	jQuery( '#bulletimg' ).hide();
	return false;
});

var files;
jQuery(document).ready(function($) {
	$('#Add_custom_font input[type=file]').on('change', prepareUpload);
});
//Grab the files and set them to our variable
function prepareUpload(event)
{
  files = event.target.files;
  uploadFiles();
}
function uploadFiles() {
	    var data = new FormData();
	    if (typeof files !== 'undefined') {
	    jQuery.each(files, function(key, value)
	    {
	        data.append(key, value);
	    });
	    }
	    var $save_status = jQuery('.ptp-js-save-status'),
	    $loading = jQuery('.ptp-js-save-loader');
	    
	    $loading.stop(true, true).fadeIn(100);
	    data.append('action', 'add_custom_font');
	    jQuery.ajax({
	    	url: ajaxurl,
	    	type: 'POST',
	    	data: data,
	        cache: false,
	        dataType: 'json',
	        processData: false, // Don't process the files
	        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
	        success: function(data, textStatus, jqXHR) {
	        	$save_status.addClass('success');
	        		jQuery('#content_font_pdf').append(
	        		    		jQuery('<option></option>').val(data.data[0]).html(data.data[1])
	        		    );
	        		jQuery('#content_font_pdf').val(data.data[0]);
	        		jQuery('#header_font_pdf').append(
        		    		jQuery('<option></option>').val(data.data[0]).html(data.data[1])
        		    );
	        		jQuery('#header_font_pdf').val(data.data[0]);
	        		jQuery('#footer_font_pdf').append(
	    		    		jQuery('<option></option>').val(data.data[0]).html(data.data[1])
	    		    );
	        		jQuery('#footer_font_pdf').val(data.data[0]);
		    },
		    error: function(jqXHR, textStatus, errorThrown) {
		    	$save_status.addClass('failed');
		    },
		    complete: function(){
		    	$loading.stop(true, true).fadeOut(100, function() {
					$save_status.stop(true, true).fadeIn(100);
				});

				setTimeout(function() {
					$save_status.stop(true, true).fadeOut(1000, function() {
						$save_status.removeClass('success').removeClass('failed');
					});
				}, 3000);
		    },
		    
	    });
}