define(function(require, exports, module){
	/**
	 * theme.init() init for the theme js
	 * 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	 
	var $ = require('modules/jquery');
	exports.config = {
		is_home : false
	
	};
	exports.init = function(){
		$(document).ready(function(){
			exports.lbox.init();
			exports.hide_no_js();
			exports.lazyload();
			//exports.zoom.init();
			// exports.home_box.init();
			exports.search();
			exports.mobile_menu.init();
			// exports.mod_tab.init();
			setTimeout(function(){exports.qrcode.init();},1000);
			exports.thumbnail_fix.init();
			exports.sidebar_float.init();
			// exports.fixed_box.init();
			
		});
	};

	exports.sidebar_float = {
		config : {
			sidebar_id : '.sidebar',
			main_id : '.main',
			footer_id : 'body > footer',
			max_screen_with : 767
		},
		sb_ori_left : false,
		sb_ori_top : false,
		sb_last_top : false,
		sb_last_bottom : false,
		sb_ori_width : false,
		sb_stop_top : false,
		init : function(){
			this.$sb = $(this.config.sidebar_id);
			if(!this.$sb[0]) return false;
			this.sb_height =  this.$sb.height();
			if($(this.config.main_id).height() <= this.sb_height) return false;
			this.$footer = $(this.config.footer_id);
			this.footer_top = this.$footer.offset().top;
			this.sb_ori_left = this.$sb.offset().left;
			this.sb_ori_top = this.$sb.offset().top;
			this.sb_ori_width = this.$sb.innerWidth();
			this.sb_stop_top = this.footer_top - this.sb_height;
			this.sb_last_top = this.$sb.offset().top;
			this.sb_last_bottom = this.sb_last_top + this.$sb.height();
			this.middle_scroll();
		},
		middle_scroll : function(){
			var _this = this,
				t = 0,
				p = 0,
				timer;
			$(window).scroll(function(){
				if(_this.is_mobile_screen()) return false;
				p = $(this).scrollTop();

				//scroll down
				if(t<=p){
					_this.middle_scroll_down();
				//scroll up
				}else{
					_this.middle_scroll_up();
				}
				setTimeout(function(){
					t = p;
				},10);
			});
			
		},
		is_mobile_screen : function(){
			if($(window).width() <= this.config.max_screen_with){
				this.$sb.css({
					position : 'static',
					top : '',
					left : '',
					bottom : '',
					width : ''
				});
				return true;
			}else{
				return false;
			}
				
		},
		middle_scroll_up : function(){
			if(!this.sb_last_top) this.sb_last_top = this.$sb.offset().top;
			//如果滚动到初始状态
			if($(window).scrollTop() <= this.sb_ori_top){
				this.$sb.css({
					position : 'static',
					left : '',
					bottom : '',
					top : '',
					width : ''
				});
				this.sb_last_top = this.$sb.offset().top;
			//如果窗口顶高 小于 边栏顶高，则浮动
			}else if($(window).scrollTop() <= this.$sb.offset().top){
				this.$sb.css({
					position : 'fixed',
					top : 0,
					bottom : '',
					left : this.sb_ori_left + 'px',
					width : this.sb_ori_width + 'px'
					
				});
				this.sb_last_top = this.$sb.offset().top;
			//如果窗口顶高 小于 边栏顶高，则绝对定位
			}else{
				this.$sb.css({
					position : 'absolute',
					left  : this.sb_ori_left + 'px',
					bottom : '',
					top : this.sb_last_top + 'px',
					width : this.sb_ori_width + 'px'
				});
				this.sb_last_top = this.$sb.offset().top;
			}
			this.sb_last_bottom = this.sb_last_top + this.$sb.height();
		},
		middle_scroll_down : function(){
			var win_bottom = $(window).height() + $(window).scrollTop();
			//如果 sb 底高 大于 footer 底高，那么 sb 只会处于 footer 之上
			if(this.sb_last_bottom >= this.$footer.offset().top){
				this.$sb.css({
					position : 'absolute',
					top : this.sb_last_top + 'px',
					bottom : '',
					left : this.sb_ori_left + 'px',
					width : this.sb_ori_width + 'px'
				});
				this.sb_last_bottom = this.$sb.offset().top + this.$sb.height();
				if(this.sb_last_bottom > this.$footer.offset().top){
					this.$sb.css({
						top : this.$footer.offset().top - this.sb_height + 'px'
					});
					this.sb_last_bottom = this.$footer.offset().top;
					this.sb_last_top = this.$footer.offset().top - this.sb_height + 1;
				}
			//如果边栏底高 大于 底栏顶高，则边栏最大底高为底栏顶高
			}else if(this.sb_last_bottom >= this.$footer.offset().top){
				this.$sb.css({
					position : 'absolute',
					left : this.sb_ori_left + 'px',
					bottom : '',
					top : this.sb_stop_top + 'px',
					width : this.sb_ori_width + 'px'
				});
				this.sb_last_bottom = this.sb_last_top + this.$sb.height();
				this.sb_last_top = this.$sb.offset().top;
			//如果窗口顶高 大于 边栏底高，则边栏随窗口浮动
			}else if(win_bottom >= this.sb_last_bottom){
				this.$sb.css({
					position : 'fixed',
					left : this.sb_ori_left + 'px',
					bottom : '0',
					top : '',
					width : this.sb_ori_width + 'px'
				});
				this.sb_last_bottom = this.sb_last_top + this.$sb.height();
				this.sb_last_top = this.$sb.offset().top;
			//如果窗口底高和底栏顶高 小于 边栏底高，则边栏顶高为上次的顶高
			}else{
				//如果已经是 fixed
				if(this.$sb.css('position') === 'fixed'){
					this.$sb.css({
						position : 'absolute',
						top : this.sb_last_top + 'px',
						bottom : '',
						left : this.sb_ori_left + 'px',
						width : this.sb_ori_width + 'px'
					});
				}
			}
		}
		
	};
	exports.zoom = {
		that : this,
		config : {
			content_reset_id : '.content-reset',
			img_id : '.content-reset a img'
			
		},
		init : function() {
			var _this = this,
				that = _this.that,
				$content_resets = $(_this.config.content_reset_id),
				$imgs = $(_this.config.img_id),
				scroll_ele = navigator.userAgent.toLowerCase().indexOf('webkit') === -1 ? 'html' : 'body';
			if(!$imgs[0]) return false;
			$content_resets.each(function(i){
				var $content_reset = $(this),
					$img = $content_reset.find('a>img'),
					$a = $img.parent(),
					content_reset_top = $content_reset.offset().top,
					img_small_src = $img.attr('src'),
					img_large_src = $a.attr('href');
				$a.on('click',function(){
					var $this = $(this),
						img_large = new Image();
					img_large.src = img_large_src;
					// load from cache
					if($this.hasClass('zoomed')){
						// 	scroll to content_reset_top
						if($(scroll_ele).scrollTop() > $content_reset.offset().top){
							$(scroll_ele).scrollTop($content_reset.offset().top - 80);
						}
						$img.attr({
							src : img_small_src,

						}).removeAttr('width')
						.removeAttr('height');
						$this.removeClass('zoomed');
						
					}else{
						var check = function(){
							if(img_large.width > 0 || img_large.height > 0){
								$img.attr({
									width : img_large.width,
									height : img_large.height
								});
								clearInterval(set);
							}
						};
						var set = setInterval(check,200);
						if(img_large.complete){
							$img.attr('src',img_large_src);
						}else{
							$img.fadeTo('slow','0.5',function(){
								if(img_large.complete){
									$img.fadeTo('fast',1)
										.attr('src',img_large_src);
								}
							});
						}
						$this.addClass('zoomed');
					}
					return false;
				});
			});
		}
	};
	/*
	exports.fixed_box = {
		config : {
			aside_id : '.widget-area aside'
		},
		init : function(){
			var $asides = $(exports.fixed_box.config.aside_id);
			if(!$asides[0]) return false;
			if(!exports.fixed_box.eligible_screen()) return false;
			$(window).resize(function(){
				exports.fixed_box.eligible_screen();
			});
			var $last_aside = $asides.eq($asides.length - 1),
				last_ot = $last_aside.offset().top,
				last_h = $last_aside.height(),
				last_w = $last_aside.width(),
				t;
				console.log(last_ot+last_h);
			$(window).scroll(function(){
				if(t) clearTimeout(t);
				t = setTimeout(function(){
					exports.fixed_box.fixed_action($last_aside,last_ot+last_h,last_w);
				},200);
			});
			
		},
		eligible_screen : function(){
			var w = $(window).width();
			if(w <= 768) return false;
			return true;
			// console.log(w);
		},
		fixed_action : function($fixed_ele,fixed_ele_ot,fixed_ele_w){
			if($(window).scrollTop() > fixed_ele_ot){
				$fixed_ele.
				addClass('aside-fixed')
				.css({
					'width' : fixed_ele_w + 'px'
				})
			}else{
				$fixed_ele.removeClass('aside-fixed');
			}
		}
	};
	*/
	exports.thumbnail_fix = {
		config : {
			
		},
		
		init : function(){
			var _this = this;
			_this.bind();
			$(window).resize(function(){
				_this.bind();
			});
		},
		bind : function(){
			var $a = $('.post-img-lists .post-list-link');
			if(!$a[0]) return false;
			var prev_h = 0;
			$a.each(function(i){
				var $this = $(this),
					w = $this.width(),
					h = $this.height(),
					new_h = Math.round(w*3/4),
					abs_h = Math.abs(prev_h - new_h);
					if(prev_h != 0 && abs_h > 0 && abs_h < 2){
						new_h = prev_h;
					}
				$this.height(new_h);
				prev_h = new_h;
			});
		}
	};
	exports.qrcode = {
		config : {
			id : '#qrcode',
			box_id : '#qrcode-box',
			zoom_id : '#qrcode-zoom'
		},
		cache : {},
		init : function(){
			var $qr = $(this.config.id);
			if(!$qr[0]) return false;
			var $box = $qr.find(this.config.box_id),
				$zoom = $qr.find(this.config.zoom_id);

			require.async(['modules/jquery.qrcode'],function(_a){
				$zoom.find('#qrcode-zoom-code').qrcode(window.location.href);
				$qr.fadeIn();
				$box.qrcode(window.location.href).on('click',function(){
					require.async(['modules/jquery.dialog'],function(dialog){
						$zoom.show();
						var d = dialog({
							title : false,
							quickClose: true,
							content : $zoom,
							fixed: true
						});
						d.show();
					});
				});
			
			});
			
		}
		
		
	};
	

	exports.search = function(){
		var $fm = $('.fm-search'),
			st = false;
		if(!$fm) return false;
		var $box = $fm.find('.box'),
			$input = $fm.find('[name="s"]');

		$fm.find('label').on('click',function(){
			if($fm.hasClass('active')){
				$fm.removeClass('active');
			}else{
				$fm.addClass('active');
				$input.focus().select();
			}
		});
		$input.on('blur',function(){
			st = setTimeout(function(){
				$fm.removeClass('active');
			},5000);
		});
		$input.on('focus',function(){
			st && clearTimeout(st);
		});
		$fm.on('submit',function(){
			if($.trim($input.val()) === ''){
				$input.focus();
				return false;
			}
		});
	};
	exports.hide_no_js = function(){
		var $no_js = $('.hide-no-js'),
			$on_js = $('.hide-on-js');
		$on_js[0] && $on_js.hide();
		$no_js[0] && $no_js.show();
		
	};
	exports.mobile_menu = {
		config : {
			toggle_menu_id : '.menu-mobile-toggle'
		},
		init : function(){
			var $toggle_menu = $(this.config.toggle_menu_id);
			if(!$toggle_menu[0]) return false;
			$toggle_menu.each(function(){
				$(this).find('a.toggle').on('click',function(){
					var $target_menu = $($(this).data('target'));
					$target_menu.toggle();
				});
			});
		}
	};
	/**
	 * lazyload for img
	 * 
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.lazyload = function(){
		var $img = $('img[data-original]');
		if(!$img[0]) return false;
		require.async(['modules/tools','modules/jquery.lazyload'],function(tools,_a){
			$img.each(function(){
				var $this = $(this);
				if(tools.in_screen($this)){
					$this.attr('src',$this.data('original'));
				}else{
					$this.lazyload();
				}
			});
		});
	};
	/**
	 * Lbox for img of post content
	 * 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	exports.lbox = {
		config : {
			img_id : '.content-reset a>img',
			no_a_img_id : '.content-reset img'
		},
		init : function(){
			var _this = this,
				$img = $(_this.config.img_id);
			if(!$img[0]) return false;
			$img.each(function(){
				$(this).parent().attr({
					'target' : '_blank',
					'rel' : 'fancybox-button'
				}).addClass('lbox');
			});
			require.async(['modules/jquery.fancybox','modules/jquery.fancybox-buttons'],function(_a,_b){
				$('.content-reset a.lbox').fancybox({
					helpers : {
						buttons	: {},
						title	: {
							type : 'float'
						}
					}
				
				});
			});
		}
	};
});