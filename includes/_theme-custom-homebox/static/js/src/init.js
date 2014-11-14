define(function(require, exports, module){
	var $ = require('modules/jquery');
	exports.config = {
		
	
	};
	exports.init = function(){
		exports.home_box.bind();
	};
	
	exports.home_box = {
		config : {
			boxes_id : '.home-box-tpl',
			add_btn_id : '#home-box-add',
			del_btn_id : '#home-box-del'
			
		},
		cache : {},
		bind : function(){
			exports.home_box.$boxes = $(exports.home_box.config.boxes_id);
			exports.home_box.hook.action_add_tpl();
			exports.home_box.hook.action_del_tpl();
			
		},
		hook : {
			action_add_tpl : function(){
				var $add_btn_id = $(exports.home_box.config.add_btn_id);
				if(!$add_btn_id[0]) return false;
				var $box_control = $('#home-box-control');
				$add_btn_id.on('click',function(){
					var $this = $(this),
						box_len = exports.home_box.$boxes.length,
						$tpl = $($this.data('tpl').replace(/%/g,box_len));
					$box_control.before($tpl);
				});
			},
			action_del_tpl : function(){
				$del_btn_id = $(exports.home_box.config.del_btn_id);
				if(!$del_btn_id[0]) return false;
				$del_btn_id.on('click',function(){
					exports.home_box.$boxes = $(exports.home_box.config.boxes_id);
					var $this = $(this),
						box_len = exports.home_box.$boxes.length;
					if(box_len === 1) return false;
					exports.home_box.$boxes.eq(box_len - 1).remove();
				});
			}
		}
	
	};
});