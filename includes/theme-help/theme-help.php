<?php
/*
Feature Name:	主题帮助与说明
Feature URI:	http://www.inn-studio.com
Version:		1.0.4
Description:	主题必须组件，显示主题相关信息与说明
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
add_action('help_settings','theme_help::admin');
add_action('after_backend_tab_init','theme_help::js'); 
add_filter('theme_options_default','theme_help::options_default');
class theme_help{
	public static $iden = 'theme_help';

	public static function options_default($options){
		
		$theme_edition = array(
			___('Free Edition'),
			___('Business Edition')
		);
		$options['theme_edition'] = $theme_edition[theme_functions::$theme_edition];
		return $options;
	}
	
	public static function admin(){
		
		$options = theme_options::get_options();
		$theme_data = wp_get_theme();
		?>
		<fieldset>
			<legend><?php echo ___('Theme Information');?></legend>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php echo ___('Theme name');?></th>
						<td><?php echo $theme_data->display('Name');?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Theme URI');?></th>
						<td><a href="<?php echo $theme_data->display('ThemeURI');?>" target="_blank"><?php echo $theme_data->display('ThemeURI');?></a></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Theme author');?></th>
						<td><?php echo $theme_data->get('Author');?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Author site');?></th>
						<td><a href="<?php echo $theme_data->display('AuthorURI');?>" target="_blank"><?php echo $theme_data->display('AuthorURI');?></a></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Theme version');?></th>
						<td><?php echo $theme_data->display('Version');?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Theme edition');?></th>
						<td><?php echo $options['theme_edition'];?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Theme description');?></th>
						<td><p><?php echo $theme_data->display('Description');?></p></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Description of each kind edition');?></th>
						<td>
							<p><strong><?php echo ___('Free version: ');?></strong><?php echo ___('The version of the theme was designed for personal users and you do not need to pay any fee to use it but we do not provide technical support. The theme that you can use a few features and online update service.');?></p>
							<p><strong><?php echo ___('Business version: ');?></strong><?php echo ___('The version of the theme was designed for paying users and you need to pay a fee to use it and we provide technical support. The theme that you can use full features and online update service.');?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html(___('Feedback and technical support'));?></th>
						<td>
							<p><?php echo esc_html(___('E-Mail'));?> <a href="mailto:kmvan.com@gmail.com">kmvan.com@gmail.com</a></p>
							<p>
								<?php echo esc_html(___('QQ (for Chinese users)'));?><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=272778765&site=qq&menu=yes">272778765</a>
							</p>
							<p>
								<?php echo esc_html(___('QQ Group (for Chinese users)'));?>
								<a href="http://wp.qq.com/wpa/qunwpa?idkey=d8c2be0e6c2e4b7dd2c0ff08d6198b618156d2357d12ab5dfbf6e5872f34a499" target="_blank">170306005</a>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('Donate');?></th>
						<td>
							<p>
								<a id="paypal_donate" href="javascript:void(0);" title="<?php echo ___('Donation by Paypal');?>">
									<img src="http://ww2.sinaimg.cn/large/686ee05djw1ella1kv74cj202o011wea.jpg" alt="<?php echo ___('Donation by Paypal');?>" width="96" height="37"/>
								</a>
								<a id="alipay_donate" target="_blank" href="http://ww3.sinaimg.cn/large/686ee05djw1eihtkzlg6mj216y16ydll.jpg" title="<?php echo ___('Donation by Alipay');?>">
									<img width="96" height="37" src="http://ww1.sinaimg.cn/large/686ee05djw1ellabpq9euj202o011dfm.jpg" alt="<?php echo ___('Donation by Alipay');?>"/>
								</a>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	<?php
	}
	public static function js(){
		
		?>
		seajs.use('<?php echo theme_features::get_theme_includes_js(__FILE__);?>',function(m){
			/** alipay */
			m.alipay.config.lang.M00001 = '<?php echo esc_js(sprintf(___('Donate to INN STUDIO (%s)'),theme_features::get_theme_info('name')));?>';
			m.alipay.config.lang.M00002 = '<?php echo esc_js(___('Message for INN STUDIO:'));?>';
			
			/** paypal */
			m.paypal.config.lang.M00001 = '<?php echo esc_js(sprintf(___('Donate to INN STUDIO (%s)'),theme_features::get_theme_info('name')));?>';
			

			m.init();
		});
		<?php
	}
}
?>