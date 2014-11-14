define(function(require, exports, module){

	exports.init = function(){
		jQuery(document).ready(function(){
			exports.bind();
		});
	};
	
	
	exports.config = {
		frm_id : '#tis_frm',
		file_id : '#tis_file',
		tip_id : '#tis_tip',
		upload_area_id : '#tis_upload_area',
		upload_link_id : '#tis_upload',
		process_url : '',
		lang : {
			M00001 : 'Processing, please wait...',
			M00002 : 'Error: Your browser does not support HTML5. ',
			E00001 : 'Error: failed to complete the operation. ',
			E00002 : 'Error: Not match file. '
		}
	};
	
	exports.bind = function(){
		if(!exports.hook.html5_check()){
			alert(exports.config.lang.M00002);
			return false;
		}
		var $upload_link = jQuery(exports.config.upload_link_id);
		jQuery(exports.config.file_id).on('mouseover mouseout mousedown mouseup change',function(event){
			if(event.type === 'mouseover'){
				jQuery(exports.config.upload_link_id).addClass('hover');
			}
			if(event.type === 'mouseout'){
				jQuery(exports.config.upload_link_id).removeClass('hover').removeClass('active');
			}
			if(event.type === 'mousedown'){
				jQuery(exports.config.upload_link_id).addClass('active');
			}
			if(event.type === 'mouseup'){
				jQuery(exports.config.upload_link_id).removeClass('active');
			}
		});
		/**
		 * html5 upload
		 */
		jQuery(exports.config.file_id)[0].addEventListener('change',exports.hook.file_select, false);
	};
	
	exports.hook = {
		/**
		 * html5_check
		 */
		 html5_check : function(){
			if(window.File && window.FileList && window.FileReader){
				return true;
			}else{
				return false;
			}
		 },
		 
		 /**
		  * file_select
		  */
		 file_select : function(e){
			var files = e.target.files || e.dataTransfer.files;
			for(var i = 0,file;file = files[i];i++){
				exports.hook.upload_file(file);
			}
		 },
		 /**
		  * upload_file
		  */
		 upload_file : function(file){
			if(file.type.indexOf('text') == 0){
				var reader = new FileReader(),
					tools = require('modules/tools');
				reader.onload = function(e){
					exports.hook.text = e.target.result;
					var ajax_data = {
						'tis_content' : e.target.result
					};
					/**
					 * start ajax
					 */
					jQuery.ajax({
						url : exports.config.process_url + '?action=tis_upload',
						type : 'post',
						dataType : 'json',
						data : ajax_data,
						beforeSend : function(){
							exports.hook.tip('loading',exports.config.lang.M00001);
						},success : function(data){
							if(data && data.status === 'success'){
								exports.hook.tip('success',data.des.content);
								location.reload(true);
							}else if(data && data.status === 'error'){
								exports.hook.tip('error',data.des.content);
							}else{
								exports.hook.tip('error',exports.config.lang.E00001);
							}
						},error : function(){
						
						}
					});
				};
				reader.readAsText(file);
			}else{
				exports.hook.tip('error',exports.config.lang.E00002);
			}
		 
		 },
		/**
		 * tip
		 */
		tip : function(type,str){
			var tools = require('modules/tools'),
				$tip = jQuery(exports.config.tip_id),
				$upload_area = jQuery(exports.config.upload_area_id);
			
			switch(type){
				case 'loading':
					$upload_area.hide();
					$tip.html(tools.status_tip('loading',str));
					break;
				case 'success':
					$upload_area.show();
					$tip.html(tools.status_tip('success',str));
					break;
				case 'error':
					$upload_area.show();
					$tip.html(tools.status_tip('error',str));
					break;
				default:
					$tip.html(str);
			}
		}
	};
	
	
	/**
	 * download
	 */
	exports.tis_download = function(){
		jQuery('#tis_export').on('click',function(){
			
		});
	};
});