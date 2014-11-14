define(function(require, exports, module){
	var $ = require('modules/jquery'),jQuery = $;
	
	
	/**
	 * validate
	 *
	 * @return object
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	exports.validate = function(){
		/** config */
		this.process_url = '';
		this.loading_tx = 'Loading, please wait...';
		this.error_tx = 'Sorry, server error please try again later.';
		this.$fm = '';
		this.rules = {};
		this.done = function(data){};
		this.before = function(){};
		this.always = function(){};
		
		var that = this;
		this.cache = {};
		this.init = function(){
			that.$fm.validate({
				rules : that.rules,
				submitHandler : function(fm){
					that.$fm = $(fm);
					that.ajax.init();
				}
			});
		};
		
		this.ajax = {
			init : function(){
				that.before();/** callback before */
				that.cache.$tip = that.$fm.find('.submit-tip');
				that.tip('loading',that.loading_tx);
				that.$fm.find('.form-group-submit').hide();
				that.$fm.find('.submit').attr('disabled',true);
				$.ajax({
					url : that.process_url,
					type : 'post',
					data : that.$fm.serialize(),
					dataType : 'json'
				}).done(function(data){
					if(data && data.status === 'success'){
						that.tip(data.status,data.msg);
						if(data.redirect){
							setTimeout(function(){
								location.href = data.redirect;
							},1000);
						}else if(exports.$_GET['return']){
							setTimeout(function(){
								location.href = exports.$_GET['return'];
							},1000);
						}
					}else if(data && data.status === 'error'){
						that.tip(data.status,data.msg);
						that.$fm.find('.form-group-submit').show();
						that.$fm.find('.submit').removeAttr('disabled');
					}else{
						that.tip(data.status,that.error_tx);
						that.$fm.find('.form-group-submit').show();
						that.$fm.find('.submit').removeAttr('disabled');
					}
					/** callback done */
					that.done(data);
				}).fail(function(){
					that.tip('error',that.error_tx);
					that.$fm.find('.form-group-submit').show();
					that.$fm.find('.submit').removeAttr('disabled');
				}).always(function(){
					/** callback always */
					that.always();
				});
			}
		};
		this.tip = function(t,s){
			if(t === 'hide'){
				that.cache.$tip.hide();
			}else{
				that.cache.$tip.html(exports.status_tip(t,s)).show();
			}
		};
		return this;
	}

	
	/** 
	 * $_GET
	 */
	exports.$_GET = {};
	document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
		function decode(s) {
			return decodeURIComponent(s.split("+").join(" "));
		}
		exports.$_GET[decode(arguments[1])] = decode(arguments[2]);
	});
	/** 
	 * String.prototype.format
	 */
	String.prototype.format = function(){    
		var args = arguments;    
		return this.replace(/\{(\d+)\}/g,                    
			function(m,i){    
				return args[i];    
			});    
	};
	/**
	 * in_screen
	 *
	 * @param object $(selector)
	 * @return bool
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 */
	exports.in_screen = function(s){
		var w = $(window);
		return !(w.scrollTop() > s.offset().top + s.outerHeight() || w.scrollTop() + w.height() < s.offset().top);
	};


	/**
	 * auto_focus
	 * 
	 * 
	 * @return $(obj) $this the focus element of jq
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.auto_focus = function($frm,selector){
		if(typeof($frm) == 'undefined' || !$frm[0]) return false;
		selector = selector || '[required]';
		$frm.find(selector).each(function(i){
			var $this = $(this);
			if(!$.trim($this.val())){
				$this.focus();
				return false;
			}
		});
	};
	/**
	 * frm_is_valid($this) 检测表单值为空
	 * 
	 * @params $this the form $ object
	 * @return object 
	 * @return object.is_invalid bool The value is null or false
	 * @return object.$this $($this) This current object
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.frm_is_valid = function($fm){
		var _this = this,
			return_data = {
				$this : false,
				is_invalid : false
			};
		fm.find("[required]").each(function(i){
			var $this = $(this);
			if(!($.trim($this.val())) && !return_data.is_invalid){
				warning_effect(100,5,function(){
					$this.css({'border-color':'red'});
				},function(){
					$this.css({'border-color':''});
				});
				$this.val('');
				$this.focus();
				return_data.is_invalid = true;
				return_data.$this = $this;
			}
		});
		
		function warning_effect(timeout,times,callback1,callback2){
			var timeout = timeout ? timeout : 150,
				times = times ? times : 5,
				i = 0;
			var si = setInterval(function(){
				/* call the callback1 */
				if(i === 0 || (i % 2 == 0)){
					callback1();
				}else{
					callback2();
				}
				if(i >= times){
					clearInterval(si);
				}
				i++;
			},timeout);
		}
		return return_data;
	};
	/**
	 * Check the value is email or not
	 * 
	 * 
	 * @params string c the email address
	 * @return bool true An email address if true
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.is_email = function(c){
		if(!c) return false;
		var b=/^([a-zA-Z0-9])*(.)*([a-zA-Z0-9])@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
		flag=b.test(c);
		if(flag){
			return true
		}else{
			return false
		}
	};
	/**
	 * status_tip
	 *
	 * @param mixed
	 * @return string
	 * @version 1.1.0
	 * @author KM@INN STUDIO
	 */
	exports.status_tip = function(){
		var defaults = ['type','size','content','wrapper'],
			types = ['loading','success','error','question','info','ban','warning'],
			sizes = ['small','middle','large'],
			wrappers = ['div','span'],
			type = null,
			icon = null,
			size = null,
			wrapper = null,
			content = null,	
			args = arguments;
			switch(args.length){
				case 0:
					return false;
				/** 
				 * only content
				 */
				case 1:
					content = args[0];
					break;
				/** 
				 * only type & content
				 */
				case 2:
					type = args[0];
					content = args[1];
					break;
				/** 
				 * other
				 */
				default:
					for(var i in args){
						eval(defaults[i] + ' = args[i];');
					}
			}
			wrapper = wrapper || wrappers[0];
			type = type ||  types[0];
			size = size ||  sizes[0];
		
			switch(type){
				case 'success':
					icon = 'checkmark-circle';
					break;
				case 'error' :
					icon = 'cancel-circle';
					break;
				case 'info':
				case 'warning':
					icon = 'warning';
					break;
				case 'question':
				case 'help':
					icon = 'help';
					break;
				case 'ban':
					icon = 'minus';
					break;
				case 'loading':
				case 'spinner':
					icon = 'spinner';
					break;
				default:
					icon = type;
			}

			var tpl = '<' + wrapper + ' class="tip-status tip-status-' + size + ' tip-status-' + type + '"><span class="icon-' + icon + '"></span><span class="after-icon">' + content + '</span></' + wrapper + '>';
			return tpl;
	}

	/** 
	 * cookie
	 */
	exports.cookie = {
		/**
		 * get_cookie
		 * 
		 * @params string
		 * @return string
		 * @version 1.0.0
		 * @author KM@INN STUDIO
		 */
		get : function(c_name){
			var i,x,y,ARRcookies=document.cookie.split(';');
			for(i=0;i<ARRcookies.length;i++){
				x=ARRcookies[i].substr(0,ARRcookies[i].indexOf('='));
				y=ARRcookies[i].substr(ARRcookies[i].indexOf('=')+1);
				x=x.replace(/^\s+|\s+$/g,'');
				if(x==c_name) return unescape(y);
			}
		},
		/**
		 * set_cookie
		 * 
		 * @params string cookie key name
		 * @params string cookie value
		 * @params int the expires days
		 * @return n/a
		 * @version 1.0.0
		 * @author KM@INN STUDIO
		 */
		set : function(c_name,value,exdays){
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + exdays);
			var c_value=escape(value) + ((exdays==null) ? '' : '; expires=' + exdate.toUTCString());
			document.cookie = c_name + '=' + c_value;
		}
	};

});