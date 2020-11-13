<?php
namespace CUBICFUSION\Plugins;

if(!defined('ABSPATH')) { exit; }

\CUBICFUSION\Core\CUBIC_HOOKS::set('MODULE', 'cf_plugins_shortcodes_widgets', (object) array(
    "name" 			=> "Shortcodes",
    "short" 		=> "Module: Dashboard Widgets->Shortcodes",
    "version" 		=> "0.2.5",
	"updated"		=> "09.11.2020",
    "description" 	=> __("<p>All dashboard widgets are converted to simple shortcodes. You can use those shortcodes within Elementor Pro or any other page builder that allows you to create custom admin dashboards.</p><p> Makes it easy to build white-label dashboards, while still reusing all those nice dashboard widgets :)</p>", 'cubicfusion-admin-enhancer' ),
	"external-links"=> array(),
    "url" 			=> "",
    "documentation" => "",
	"basic_style"	=> '.test{color: white;}'
));

class Shortcodes {
	
	
	function init(){
			  	
		add_action('wp_dashboard_setup', array($this,'tweak_dashboard'), 99);
  		add_action('cmb2_admin_init', array($this,'register_my_admin_page'));		
		add_action('admin_enqueue_scripts', array($this, 'admin_assets'));	
		add_action( 'cmb2_before_options-page_form_cf_plugins_shortcodes_widgets', 'CUBICFUSION\Core\GUI::cmb2_before_form', 10, 2 );
		add_action( 'cmb2_after_options-page_form_cf_plugins_shortcodes_widgets', 'CUBICFUSION\Core\GUI::cmb2_after_form', 10, 2 );		
		
	}	
	
	function admin_assets( $hook ) {
 
		if ( 'index.php' != $hook ) {
        	return;
		}
		
		if(cmb2_get_option( 'cf_plugins_shortcodes_widgets', 'dashboard_standard_hide' )){				
			wp_enqueue_style('cf-admin-styles', plugin_dir_url( dirname(__FILE__) ).'assets/css/shortcodes.widgets.css');
    		wp_enqueue_script( 'cf-admin-script', plugin_dir_url( dirname(__FILE__) ).'assets/js/shortcodes.widgets.min.js', array(), '1.0' );
		}
	}
	
	public static
	function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	function tweak_dashboard(){
		global $wp_meta_boxes;
				
		$buffer = array();
		foreach($wp_meta_boxes['dashboard']['normal']['core'] as $key => $widget){
			$closure = false;
			if ( $widget['callback'] instanceof \Closure ){
				continue;
				//$widget['callback'] = \CUBICFUSION\Core\Basics::prepSerializeClosure($widget['callback']);
				//$closure = true;
			}
			
			add_shortcode( 'dashboard_widget_'.$key , $widget['callback'] );
			
			$buffer[$key] = array('key' => $key,
								'name' 	=>  $widget['title'],
								 'class' => 'normal',
								  'callback' => $widget['callback'],
								  'closure' => $closure 
							   );
			
			if(cmb2_get_option( 'cf_plugins_shortcodes_widgets', $key.'_disabled' )){
				remove_meta_box( $key , 'dashboard', 'normal' );
			}	   
			
			
		}
		
		foreach($wp_meta_boxes['dashboard']['side']['core'] as $key => $widget){
			
			if ( $widget['callback'] instanceof \Closure ){
				continue;
				//$widget['callback'] = \CUBICFUSION\Core\Basics::prepSerializeClosure($widget['callback']);	
				//$closure = true;
			}
			
			add_shortcode( 'dashboard_widget_'.$key , $widget['callback'] );
			
			$buffer[$key] = array('key' => $key,
								'name' 	=>  $widget['title'],
								  'class' => 'side',
								  'callback' => $widget['callback'],
								   'closure' => $closure 
							   
							   );
			if(cmb2_get_option( 'cf_plugins_shortcodes_widgets', $key.'_disabled' )){
				remove_meta_box( $key , 'dashboard', 'side' );
			}	
		}		
	
		add_option('cf_plugins_shortcodes_cache_widgets', $buffer);	
		
	}
	
	function register_my_admin_page() {
  
  		\CUBICFUSION\Core\Basics::admin_can_edit();

        $secondary_options = new_cmb2_box( array(
              'id'           => 'cf_plugins_shortcodes_widgets',
              'title'        => esc_html__( 'Shortcodes', 'cmb2' ),
			  'menu_title'	 =>  'Shortcodes',
              'object_types' => array( 'options-page' ),
              'option_key'   => 'cf_plugins_shortcodes_widgets',
              'parent_slug'  => 'cf_plugins_shortcodes_options',
          ) );
      
	        $secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-admin-settings"></span> '.__('Dashboard', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'general_title'
              ) );

         $secondary_options->add_field( array(
            'name' => __('Hide Standard Dashboard Widgets & Title', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'dashboard_standard_hide',
            'type' => 'checkbox',
        ) );
		
		$save = get_option( 'cf_plugins_shortcodes_cache_widgets' );
		
        if(!empty($save) ) {
          foreach($save as $widget){

              $secondary_options->add_field( array(
                'name' => '<span class="dashicons dashicons-welcome-widgets-menus"></span> '.$widget['name'],
                //'desc' => 'This is a title description',
                'type' => 'title',
                'id'   => $widget['name'].'_title'
                  ) );

              $secondary_options->add_field( array(
              'name'    => __('Shortcode', 'cubicfusion-admin-enhancer' ),
              //'desc'    => 'field description (optional)',
              'default' => "[dashboard_widget_".$widget['key']."]",
              'id'      => $widget['key'],
              'type'    => 'text',
              'attributes' => array(
                  'readonly' => 'readonly'),
              'after' => array($this,  'send_to_clipboard'),
              ) );

              $secondary_options->add_field( array(
                'name' => __('Deactivate Widget', 'cubicfusion-admin-enhancer' ),
                //'desc' => 'field description (optional)',
                'id'   =>  $widget['key'].'_disabled',
                'type' => 'checkbox',
            ) );
           }
		}
	}
	
	function send_to_clipboard(){
		echo ' <span title="'.__('Copy me to clipboard!', 'cubicfusion-admin-enhancer' ).'" class="cf gui_clipboard dashicons dashicons-clipboard"></span>';
	}
}