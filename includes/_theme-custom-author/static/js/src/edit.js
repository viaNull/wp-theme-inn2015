define(function(require, exports, module){
	var $ = require('modules/jquery'),
		tools = require('modules/tools'),
		js_request 	= require('theme-cache-request');
		require('modules/jquery.validate');
		require('modules/jquery.validate.lang.{locale}');
	
	exports.config = {
		fm_id : '.fm-edit',
		profile_fm_id : '#profile-edit',
		pwd_fm_id : '#change-pwd',
		process_url : '',
		lang : {
			M00001 : 'Loading, please wait...',
			E00001 : 'Server error or network is disconnected.'
		}
	};
	exports.cache = {};
	exports.init = function(){
		$(document).ready(function(){
			exports.cache.$profile_fm = $(exports.config.profile_fm_id);
			var process_url = exports.config.process_url + '&' + $.param({
				'theme-nonce' : js_request['theme-nonce']
			});
			if(exports.cache.$profile_fm[0]){
				var profile_v = new tools.validate();
					profile_v.process_url = process_url;
					profile_v.loading_tx = exports.config.lang.M00001;
					profile_v.error_tx = exports.config.lang.E00001;
					profile_v.$fm = exports.cache.$profile_fm;
					profile_v.init();
			}
			exports.cache.$pwd_fm = $(exports.config.pwd_fm_id);
			if(exports.cache.$pwd_fm[0]){
				var pwd_v = new tools.validate();
					pwd_v.process_url = process_url;
					pwd_v.rules = {
						'user[pwd-again]' : {
							equalTo : '#edit-pwd-new'
						}
					};
					pwd_v.loading_tx = exports.config.lang.M00001;
					pwd_v.error_tx = exports.config.lang.E00001;
					pwd_v.$fm = exports.cache.$pwd_fm;
					pwd_v.init();
			}
		});
	};
});