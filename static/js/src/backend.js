define(function(require, exports, module){
	$ = require('modules/jquery');

	/**
	 * admin page init js
	 */
	exports.init = function(){
		$(document).ready(function(){
			exports.backend_tab.init();
		});
	};
	/**
	 * Select Text
	 * 
	 * 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.select_text = {
		config : {
			select_text_id : '.text-select'
		},
		init : function(){
			exports.select_text.bind();
		},
		bind : function(){
			$select_text = $(exports.select_text.config.select_text_id);
			if($select_text[0]){
				$select_text.on('click',function(event){
					$(this).select();
				});
			}
		}
	};
	/**
	 * Admin Tab
	 * 
	 * 
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.backend_tab = {
		config : {
			tab_id : '#backend-tab',
			tab_cookie_id : 'backend_default_tab'
		},
		init : function(){
			exports.backend_tab.bind();
		},
		bind : function(){
			var $tab = $(exports.backend_tab.config.tab_id);
			if(!$tab[0]) return false;
			require('modules/jquery.kandytabs');
			var tools = require('modules/tools'),
				current_tab;
			current_tab = tools.cookie.get(exports.backend_tab.config.tab_cookie_id);
			if(!current_tab) current_tab = 1;
			$tab.KandyTabs({
				delay:100,
				resize:false,
				custom:function(b,c,i,t){
					tools.cookie.set(exports.backend_tab.config.tab_cookie_id,i+1);
				},
				current:current_tab,
				done:function(){
					/**
					 * set cookie for current tab
					 */
					if(!(tools.cookie.get(exports.backend_tab.config.tab_cookie_id))){
						tools.cookie.set(exports.backend_tab.config.tab_cookie_id,'0');
					}
					/**
					 * call done()
					 */
					exports.backend_tab.done();
					exports.select_text.init();

					/**
					 * add slide effive
					 */
					$('.backend-tab-loading').hide();
					$tab.show();
				}
			});			
		},
		done:function(){}
	};

});