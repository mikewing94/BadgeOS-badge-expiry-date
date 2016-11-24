jQuery(document).ready(function(jQuery) {

	// Retrieve credly category results via AJAX
	jQuery('input[name^="badges_expiry_action"]').click( function( event ) {

		// Stop the default submission from happening
		event.preventDefault();

		// Grab our form values
		//var search_terms = jQuery('#credly_category_search').val();
    
    var data = { 
      action: 'update_badges_validity',
      security: ajax_nonce,
      //search_terms: search_terms 
    };
    //jQuery('input[id^=badges_expiry_action]').closest('tr').find('input[type=text]').each(function (index, value) {
    jQuery(this)
      .closest('tr')
      .find('input[type=text], input[type=hidden]')
      .each(function (index, obj) {
        if (jQuery(obj).data('field_name') != '') {
          data[jQuery(obj).data('field_name')]  = jQuery(obj).val();
        }
      });

		jQuery.ajax({
			type : "post",
			dataType : "json",
			url : ajaxurl,
			data : data,
			success : function(response) {
				 //jQuery('#credly-badge-settings fieldset').append(response);
				 //jQuery('#credly_search_results').show();
         jQuery( "#badgeos_badge_expiry_settings_loading" ).dialog('close');
			}
		});
    jQuery( "#badgeos_badge_expiry_settings_loading" ).dialog({
      autoOpen: true,
      height: 100,
      width: 100,
      modal: true,
      title: 'Updating'
    });
	});

});