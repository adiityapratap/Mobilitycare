<?php
/******************************************************
 * @package	SO Theme Framework for Opencart 2.3.x
 * @author	http://www.magentech.com
 * @license	GNU General Public License
 * @copyright(C) 2008-2015 Magentech.com. All rights reserved.
*******************************************************/


require_once (DIR_APPLICATION.'view/template/extension/soconfig/class/so_field.php');
require_once (DIR_APPLICATION.'view/template/extension/soconfig/class/soconfig.php');
require_once (DIR_APPLICATION.'view/template/extension/soconfig/class/soconfig_cache.php');

class ControllerExtensionModuleSoconfig extends Controller {

    private $error = array();
	private $typeheader = array();
	private $typefooter = array();
	public $typelayout = array();
	 
	public function  __construct($registry) { 
		parent::__construct($registry);
		$this->soconfig = new Soconfig($registry);
		$data['error'] = $this->error;
		
		$this->soconfig->cache = new SoconfigCache($registry);
		if(!defined('DIR_SOCONFIG')) define('DIR_SOCONFIG','view/template/extension/soconfig/');
		if(!defined('PATH_SOCONFIG')) define('PATH_SOCONFIG',DIR_APPLICATION.'view/template/extension/soconfig/');

		//Dev Custom Theme
		$this->listColor= array(
			'orange'    =>'#fa381d',
			'red' =>'#f33333',
			'blue'   =>'#1071dc',
			'pink' => '#fc9596',
			'green' => '#5bb300',
			'yellow' => '#f4a137',						
		);
		
		$this->typelayouts = array(
			array(
			'key'=>'1',
			'typelayout'=>'<p>Layout 1</p> ',
			'typeheader'=> array('key'=>'1', 'title'=>'Header 1 (used in Layout 1)'),
			'typefooter'=> array('key'=>'1', 'title'=>'Footer 1 (used in Layout 1)'),
			),
			array(
			'key'=>'2',
			'typelayout'=>'<p>Layout 2</p>',
			'typeheader'=> array('key'=>'2', 'title'=>'Header 2 (used in Layout 2)'),
			'typefooter'=> array('key'=>'2', 'title'=>'Footer 2 (used in Layout 2)'),
			),
			array(
			'key'=>'3',
			'typelayout'=>'<p>Layout 3 </p>',
			'typeheader'=> array('key'=>'3', 'title'=>'Header 3 (used in Layout 3)'),
			'typefooter'=> array('key'=>'3', 'title'=>'Footer 3 (used in Layout 3)'),
			),
			array(
			'key'=>'4',
			'typelayout'=>'<p>Layout 4 </p>',
			'typeheader'=> array('key'=>'4', 'title'=>'Header 4 (used in Layout 4)'),
			'typefooter'=> array('key'=>'4', 'title'=>'Footer 4 (used in Layout 4)'),
			),
			array(
			'key'=>'5',
			'typelayout'=>'<p>Layout 5 </p>',
			'typeheader'=> array('key'=>'5', 'title'=>'Header 5 (used in Layout 5)'),
			'typefooter'=> array('key'=>'5', 'title'=>'Footer 5 (used in Layout 5)'),
			),
		
			
			
		);
	}
    public function index() {
		/*===== Load language ========== */
		$this->load->language('extension/module/soconfig');
		$data['direction'] = $this->language->get('direction');
		$data['objlang'] = $this->language;

		/*===== Load Title Module ========== */
		$this->document->setTitle($this->language->get('heading_title_normal'));
		
		/*===== Load CSS & JS ========== */
		$this->document->addScript(DIR_SOCONFIG.'asset/plugin/colorpicker/bootstrap-colorpicker.min.js');
	
		$this->document->addScript(DIR_SOCONFIG.'asset/js/libs.js');
		$this->document->addScript(DIR_SOCONFIG.'asset/js/webfont.js'); 
		$this->document->addScript(DIR_SOCONFIG.'asset/plugin/select2/js/select2.min.js'); 
		$this->document->addScript(DIR_SOCONFIG.'asset/js/common.js');
		$this->document->addScript(DIR_SOCONFIG.'asset/js/theme.js');
		
		
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/summernote/summernote-image-attributes.js');
		$this->document->addScript('view/javascript/summernote/opencart.js');
		
		
   	 	$this->document->addStyle(DIR_SOCONFIG.'asset/plugin/colorpicker/bootstrap-colorpicker.min.css');
    	$this->document->addStyle(DIR_SOCONFIG.'asset/plugin/select2/css/select2.min.css'); 
		$this->document->addStyle('view/javascript/summernote/summernote.css');
		$this->document->addStyle(DIR_SOCONFIG.'asset/css/flexbox.css');
		$this->document->addStyle(DIR_SOCONFIG.'asset/css/banner-effect.css');
		
		/*===== Check RTL Css ========== */
        $this->document->addStyle(DIR_SOCONFIG.'asset/css/theme.css');
        if ($data['direction'] == 'rtl') $this->document->addStyle(DIR_SOCONFIG.'asset/css/rtl.css');
		
		/*===== Load model ========== */
		$this->load->model('setting/store');
        $this->load->model('setting/setting');
		$this->load->model('extension/module/soconfig/setting');
		$this->load->model('design/layout');
		$this->load->model('tool/image');
		$this->load->model('localisation/language');
		
		/*===== Load Stores========== */
		$store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        $stores = $this->model_setting_store->getStores();
		
		array_unshift($stores, array('store_id' => '0','name'     => $this->config->get('config_name'),'url'  => $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG,'status'=>'1'));
		foreach ($stores as $store) {
			$store_data[] = array(
				'name'   => $store['name'],
				'store_id' =>  $store['store_id'],
				'status' => $this->model_setting_setting->getSettingValue('theme_default_status', $store['store_id']) ,
				'url'      => $store['url'],
			);
		}
		$data['stores'] = $store_data;
		$data['active_store'] = $store_id;
		/*===== End Load Stores========== */
	
		if (  $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate() ) {			
            // ButtonForm apply
			if($this->request->post['buttonForm'] == 'color' ){
				$scsscompile = $this->request->post['soconfig_advanced_store']['scsscompile'];
				
				if($scsscompile == 0){
					unset($this->request->post['buttonForm']);
					$this->session->data['success'] = 'Success Compile Sass File To Css';
					$nameColor   = str_replace(' ', '-', strtolower($this->request->post['soconfig_advanced_store']['name_color']));
					$this->soconfig->scss_compass($this->request->post['soconfig_advanced_store']['theme_color'],$nameColor ,$this->request->post['soconfig_general_store']['typelayout'],$this->request->post['soconfig_advanced_store']['compileMutiColor'], $this->listColor );
					$this->response->redirect($this->url->link('extension/module/soconfig','user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true));
				}else{
					$this->session->data['success'] = 'Error: Compile Sass File To Css, Select Performace -> SCSS Compile = Off';
				}
				
			}else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }
		}
		
		
        if (isset($this->session->data['success'])) {
        	$data['success'] = $this->session->data['success'];
        	unset($this->session->data['success']);
        } else {
        	$data['success'] = '';
        }
		
		
		$data['module_modify']= $this->user->hasPermission('modify', 'extension/module/soconfig'); 
		$data['homeStyle'] = $this->typelayouts;
		$data['dir_soconfig'] = DIR_SOCONFIG;
		$data['base_href'] = $this->url->link('extension/module/soconfig', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['action'] = $this->url->link('extension/module/soconfig', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true);
		$data['clear_cache'] = $this->url->link('extension/module/soconfig/clearcache', 'user_token='. $this->session->data['user_token'].'&store_id='.$store_id, true);
		$data['add_db_product'] = $this->url->link('extension/module/soconfig/addCustomProduct', 'user_token='. $this->session->data['user_token'].'&store_id='.$store_id, true);
		$data['href_product'] = $this->url->link('catalog/product', 'user_token='. $this->session->data['user_token'].'&store_id='.$store_id, true);
		$data['href_preview'] = HTTP_CATALOG;
		$data['status_product_option']= false;
		$column_exists_tab_title = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product_description LIKE 'tab_title'");
		if ($column_exists_tab_title->num_rows)  $data['status_product_option']= true;

		/*===== Default Config Theme Stores========== */
		$default = array(
			'soconfig_advanced_store'	=> array(
				'newcolor_status'	=> 1,
				'name_color'	=>'blue',
				'theme_color'	=>'#1f83cf',
				'scsscompile'	=>0,
				'scssformat'	=>'Nested',
				'compileMutiColor'		=>0,
				'cssminify'	=>0,
				'cssExclude'	=> array(
					array(
						'namecss' => 'catalog/view/javascript/font-awesome/css/font-awesome.min.css',
					)
				),
				'jsminify'	=>0,
				'jsExclude'	=> array(
					array(
						'namecss' => 'catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js',
					)
				),
			),
			'soconfig_general_store'	=> array(
				'typelayout'	=>1,
				'themecolor'	=>'blue',
				'type_banner'	=>4,
				'preloader'	=>1,
				'preloader_animation'	=>'line',
				'toppanel_status'	=>1,
				'toppanel_type'	=>1,
				'custom_status'		=>1,
				'custom_html' 	=> ' ',
				'header_socials'		=>1,
				'custom_socials' 	=> ' ',
				'welcome_message_status'	=>1,
				'welcome_message' 	=> array(
					'1'=>'Welcome Message English',
					'2'=>'Welcome Message Arabic',
			   	),
				'iconWishlist_status'	=>1,
				'iconCompare_status'	=>1,
				'imgpayment_status'	=>1,
				'imgpayment'	=>'catalog/demo/payment/payment.png',
				'copyright_status'	=>1,
				'copyright' 	=> array(
					'1'=>'So Theme © {year}. All Rights Reserved. Designed by &lt;a href="magentech.com"&gt;MagenTech.Com&lt;/a&gt;',
					'2'=>'So Theme © {year}. All Rights Reserved. Designed by &lt;a href="magentech.com"&gt;MagenTech.Com&lt;/a&gt;',
			   	),
			),
			'soconfig_layout_store'	=> array(
				'layoutstyle'	=>'full',
				'theme_bgcolor'	=>'#7a167a',
				'contentbg'	=>'',
				'content_bg_mode'	=>'repeat',
				'content_attachment'		=>'scroll',
				'iconcart'		=>'cart-6',
				'headercolor_status'		=>0,
				'headerparent'		=>' ',
				'headertop'		=>' ',
				'headercenter'		=>' ',
				'headerbottom'		=>' ',
			),
			'soconfig_pages_store'	=> array(
				'catalog_col_position'	=>'outside',
				'catalog_col_type'	=>'default',
				'catalog_sidebar_sticky'	=>'disable',
				'lstimg_cate_status'	=>1,
				'product_catalog_refine'	=>0,
				'featured_cate_status'	=>0,
				'catalog_refine_number'	=>6,
				'product_catalog_mode'	=>'grid-4',
				'card_gallery'				=>2,
				'desktop_addcart_position'			=>'left',
				'placeholder_status'	=>1,
				'placeholder_img'  =>'catalog/logo.png',
				'listing_quickview_status'			=>1,
				'listing_cart_status'			=>1,
				'listing_wishlist_status'			=>1,
				'listing_compare_status'			=>1,
				'listing_discount_status'		=>1,
				'listing_order_status'		=>1,
				'listing_countdown_status'		=>1,
				'listing_gallery_status'		=>1,
				'listing_rating_status'			=>1,
				'product_order'	=>1,
				'radio_style'			=>1,
				'thumbnails_position'			=>'bottom',
				'product_enablezoom'	=>1,
				'product_enablesizechart'		=>1,
				'img_sizechart'  =>'catalog/logo.png',
				'tabs_position'  =>2,
				'product_enableshowmore'  =>0,
				'product_enableshipping'  =>0,
				'product_contentshipping' 	=> array(
					'1'=>'Custom block Html',
					'2'=>'Custom block Html',
			   	),
				'product_page_button'  =>0,
				'product_socialshare' 	=> array(
					'1'=>'Custom block Html',
					'2'=>'Custom block Html',
			   	),
				'related_status'  =>1,
				'product_related_column_lg'	=>4,
				'product_related_column_md'	=>3,
				'product_related_column_sm'	=>2,
				'product_related_column_xs'	=>1,
				'comingsoon_imglogo'	=>'catalog/logo.png',
				'comingsoon_title'	=> array(
					'1'=>'Coming Soon Title',
					'2'=>'Coming Soon Title',
			   	),
				'comingsoon_date'	=>'2018-05-26',
				'comingsoon_content'	=> array(
					'1'=>'Coming soon content',
					'2'=>'Coming soon content',
			   	),
				'mapaddress'	=>'',
				'mapgeocode'	=>'',
				'mapkeys'	=>'',
				'mapzoom'	=>'',
			),
			'soconfig_fonts_store'	=> array(
				'body_status'	=>1,
				'body_font'	=>'{"fontFamily":"Open Sans","fontSize":"14","fontWeight":"400","fontStyle":"normal","fontSubset":"vietnamese"}',
				'h1_font_status'	=>0,
				'h1_font'	=>' ',
				'h2_font_status'	=>0,
				'h2_font'		=>'',
				'h3_font_status'	=>0,
				'h3_font'		=>'',
				'h4_font_status'	=>0,
				'h4_font'		=>'',
				'navigation_font_status'	=>0,
				'navigation_font'		=>'',
				'moduleTitle_font_status'	=>0,
				'moduleTitle_font'		=>'',
				'productTitle_font_status'	=>0,
				'productTitle_font'		=>'',
				'productPrice_font_status'	=>0,
				'productPrice_font'		=>'',
				'custom_font_status'	=>0,
				'custom_font'		=>'',
			),
			'soconfig_social_store'	=> array(
				'social_sidebar'	=>1,
				'social_fb_status'	=>1,
				'facebook'	=>'magentech',
				'social_twitter_status'	=>1,
				'social_custom_status'	=>1,
				'twitter'	=>'smartaddons',
				'video_code' 	=>'_3C6DK8n0mQ',
				'social_instagram_status' 	=>1,
				'instagram_user' 	=>'',
				'instagram_token' 	=>'',
			),
			'soconfig_custom_store'	=> array(
				'cssinput_status'	=>0,
				'cssinput_content'	=>' ',
				'jsinput_status'	=>0,
				'jsinput_content'	=>' ',
			),
		);

		if (($this->request->server['REQUEST_METHOD'] != 'POST') || $this->request->server['REQUEST_METHOD'] == 'POST'  ) {
			$soconfig_setting = $this->model_extension_module_soconfig_setting->getSetting($store_id);
			$soconfig_setting =  array_merge($default,$soconfig_setting);//check data empty database
		}
		//Get theme_directory
		/*if ($this->config->get('theme_default_directory')) $data['theme_directory'] = $this->config->get('theme_default_directory'); 
		else $data['theme_directory'] = 'default';*/
		$data['cssExcludes'] = isset($soconfig_setting['soconfig_advanced_store']['cssExclude']) ? $soconfig_setting['soconfig_advanced_store']['cssExclude'] : array();
		$data['jsExcludes'] = isset($soconfig_setting['soconfig_advanced_store']['jsExclude']) ? $soconfig_setting['soconfig_advanced_store']['jsExclude'] : array();
		$typelayout 				= $soconfig_setting['soconfig_general_store']['typelayout'];
		$data['scsscompile'] 		= $soconfig_setting['soconfig_advanced_store']['scsscompile'];
		$data['totalLayoutColor'] 		=  $this->soconfig->getNameColor($typelayout) ? $this->soconfig->getNameColor($typelayout) : array('none' => 'No Color');
        $data['user_token'] 		= $this->session->data['user_token'];
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		
		$data['validate']= $this->validate();
		$data['error'] = $this->error;
		
		$data['fields'] = new So_Fields($soconfig_setting,$this->registry);
		$this->response->setOutput($this->load->view('extension/soconfig/soconfig', $data));
	}
	
	public function uninstall() {
        $this->load->model('extension/module/soconfig/setting');
        $this->model_extension_module_soconfig_setting->deleteSetting();
    }
	
    public function install(){
		$this->load->model('setting/setting');
		$this->load->model('extension/module/soconfig/setting');
		$this->model_extension_module_soconfig_setting->createTableSoconfig();
		
		//Import sample data current theme
		$main_sql = DIR_TEMPLATE.'extension/soconfig/demo/default/install.php';
		if (!file_exists($main_sql)) return false;   
		include($main_sql);
		
		$this->session->data['success'] = $this->language->get('text_success');
    }
	
	
     public function install_demo($install_layout,$store_active){
		$store_id = $store_active;
		$main_sql = DIR_TEMPLATE.'extension/soconfig/demo/layout'.$install_layout.'/install.php';
		if (!file_exists($main_sql)) return false;   
		
		include($main_sql);
		return true;  
    }
	
	public function addCustomProduct(){
		$this->load->model('extension/module/soconfig/soproduct');
		$this->model_extension_module_soconfig_soproduct->createColumnsInProducts();
	    $this->session->data['success'] = 'You have add field (video, tab_title, html_product_tab) for table product_description';
	    $this->response->redirect($this->url->link('extension/module/soconfig', 'user_token=' . $this->session->data['user_token'], 'SSL'));
    }
	
	public function clearcache(){
	    $this->soconfig->cache->clear();
	    $this->session->data['success'] = 'Cache cleared';
	    $this->response->redirect($this->url->link('extension/module/soconfig', 'user_token=' . $this->session->data['user_token'], 'SSL'));
    }
	

	public function saveThemeConfig(){
		
		$jsonData = array();
		$this->load->model('extension/module/soconfig/setting');
		$store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
		$data = $_POST;
		
		if($data && $this->validate()){
			$this->model_extension_module_soconfig_setting->editSetting($data, $store_id);
		}
		$jsonData= array(
			'status'        => 'true',
			'message'        => 'Success: You have  apply Themeconfig',
		);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($jsonData));
	}
	
	public function applyBaseHome() {
		$this->load->model('extension/module/soconfig/setting');
		$json = array();
		
		if (isset($this->request->get['keylayout']) && $this->validate()) {
			$keylayout = $this->request->get['keylayout'];
			$store_active = $this->request->get['store_id'];
			$this->install_demo($keylayout,$store_active);
		}
		$this->session->data['success'] = 'Success: You have  apply layout';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getTotalColorScheme() {
		$this->load->model('extension/module/soconfig/setting');
		$json = array();

		if (isset($this->request->get['keylayout'])) {
			$keylayout = $this->request->get['keylayout'];
			$totalColor = $this->soconfig->getNameColor($keylayout);

			if(!empty($totalColor)){
				foreach ($totalColor as $color) {
					$json[] = array('name'  => strtolower($color));
				}
			}else{
				$json[] = array('name'=> 'No Value');
			}
			
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	
	
	public function changeFontVariants(){
        $jsonData = array();
        $font_name  =  $this->request->get['fontName'];
        $plugin_path   =  DIR_TEMPLATE.'extension/soconfig/asset/webfonts/webfonts.json'; 

        if(file_exists( $plugin_path )){
            $json = file_get_contents( $plugin_path );
        }

        $webfonts   = json_decode($json);
        $items      = $webfonts->items;

        foreach ($items as $item)
        {
            if ($item->family == $font_name)
            {
                $fontVariants = '';
                $fontSubsets = '';
                //Variants
                foreach ($item->variants as $variant)
                {
                    $fontVariants .= '<option value="'. $variant .'">' . $variant . '</option>';
                }
                //Subsets
                foreach ($item->subsets as $subset)
                {
                    $fontSubsets .= '<option value="'. $subset .'">' . $subset . '</option>';
                }
				
				$jsonData= array(
					'status'        => 'true',
					'message'        => 'Font Style Changed',
					'variants'        => $fontVariants,
					'subsets'        => $fontSubsets
				);
                break;
            }
        }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($jsonData));
    }
	
	public function updateGoogleFontList(){
        $jsonFont = array();
        $template_path = DIR_TEMPLATE.'extension/soconfig/asset/webfonts';
		
        if (!file_exists( $template_path )) {
			mkdir($template_path, 0755);
            
        }else{
			$urlWebFonts  = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBVybAjpiMHzNyEm3ncA_RZ4WETKsLElDg';
			
			/* The code to get the google web fonts list goes here */
			$WebFonts = curl_init($urlWebFonts);
			curl_setopt($WebFonts, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($WebFonts, CURLOPT_HEADER, 0);
			curl_setopt($WebFonts, CURLOPT_SSL_VERIFYPEER, false);
			$content = curl_exec($WebFonts);
			$webfontsfile = fopen($template_path . '/webfonts.json',"w");
			fwrite($webfontsfile,$content);
			fclose($webfontsfile);
			$jsonFont= array(
				'status'        => 'true',
				'message'        => '<p class="font-update-success">Google Webfonts list successfully updated! Please refresh your browser.</p>',
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($jsonFont));
    }
	
    protected function validate() {
    	if (!$this->user->hasPermission('modify', 'extension/module/soconfig')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		/*if (empty($this->request->post['soconfig_general_store']['copyright']) ) {
			$this->error['copyright'] = $this->language->get('error_nameColor');
		}*/
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		return !$this->error;
	}
}
