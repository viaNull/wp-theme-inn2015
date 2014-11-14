define(function(require, exports, module){

	exports.config = {
		id : '',
		process_url : ''
	};
	exports.init = function(){
		jQuery(document).ready(function(){
			var ajax_data = {
				action : 'add_postview',
				id : exports.config.id
			};
			jQuery.ajax({
				url:exports.config.process_url,
				dataType:'json',
				data:ajax_data
			});
		});
	};

});