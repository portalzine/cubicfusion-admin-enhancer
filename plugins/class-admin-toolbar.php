<?php
namespace CUBICFUSION\Plugins;

if(!defined('ABSPATH')) { exit; }

\CUBICFUSION\Core\CUBIC_HOOKS::set('MODULE', 'cf_plugins_admin_toolbar', (object) array(
    "name" 			=> "Admin Toolbar",
    "short" 		=> "Module: Admin Toolbar & Footer",
    "version" 		=> "0.2",
	"updated"		=> "30.07.2021",
    "description" 	=> __("<p>This Addon allows you to tweak the admin toolbar and footer.<br><br><br></p>", 'cubicfusion-admin-enhancer' ),
	"external-links"=> array(),
    "url" 			=> "",
    "documentation" => "",
	"style"			=> 1 //'.mo{color: red;}'
));

class Admin_Toolbar {
	
	
	function init(){		

		
  		add_action('cmb2_admin_init', array($this,'register_my_admin_page'), 70);				
		add_action('admin_enqueue_scripts', array($this, 'admin_assets'));	
		add_action( 'cmb2_before_options-page_form_cf_plugins_admin_toolbar', 'CUBICFUSION\Core\GUI::cmb2_before_form', 10, 2 );
		add_action( 'cmb2_after_options-page_form_cf_plugins_admin_toolbar', 'CUBICFUSION\Core\GUI::cmb2_after_form', 10, 2 );			
		add_action('admin_init', array($this, 'additional_admin_color_schemes' ));
		$this->handleSettings();
		
	}
	// Cleanup and move & make editable
	function additional_admin_color_schemes() {
        //Get the theme directory
        $theme_dir = plugin_dir_url( dirname(__FILE__) ).'assets/css';

        //Ocean
        wp_admin_css_color( 'cubicfusion', __( 'cubicFUSION' ),
          $theme_dir . '/admin-colors/cubicfusion.css',
          array( '#222', '#333', '#18bc9c', '#00a0d2' )
        );
      }

	
	public static
	function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	
	function admin_assets( $hook ) {		
		
		wp_enqueue_style('cf-admin_toolbar-styles', plugin_dir_url( dirname(__FILE__) ).'assets/css/admin.toolbar.css');
    	wp_enqueue_script( 'cf-admin_toolbar-script', plugin_dir_url( dirname(__FILE__) ).'assets/js/admin.toolbar.min.js', array(), '1.0' );
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own' ) !== false ){
			add_action('admin_head', function () {
            	$size = explode("_",\CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_size' ));
				
				if($height = \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_height' )){
					$size[1] = $height;
				}
				echo '<style>
                      #wpadminbar{
                          left: '.$size[0].'px;
						  width: calc(100% - '.$size[0].'px);
                      }

                      #adminmenuwrap{
                         margin-top: '.($size[1] / 2).'px!important;
                      }

                      #adminmenuback::before{
                          content:"';
						  if($text = \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_text' )){
							  echo $text;
						  }
						 
				echo'";'; 
				 if($text = \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_text' )){
							  echo "line-height:".$size[1].'px;';
					  
						  }
				echo ' text-align: center;
                          position: fixed;
						  color:'. \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_txtcolor' ).';
                          top:0;
                          left:0;
						  font-weight: 600;
						  width: '.$size[0].'px;
                          height:'.$size[1].'px;                        
                          background-color: '. \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_bgcolor' ).';';
				 if($text = \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_img' )){
				echo'	  background-image: url('. \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_add_your_own_img' ).');
						  background-size: contain;';
				 }
				echo'
                      }
              </style>';
            });

		}
		
		add_action( 'wp_before_admin_bar_render',function(){
			 global $wp_admin_bar;
				add_option('cubicfusion_admin_toolbar_nodes', $wp_admin_bar->get_nodes());	
		});		
	}
	
	function register_my_admin_page() {
  
  		\CUBICFUSION\Core\Basics::admin_can_edit();
		
	    $secondary_options = new_cmb2_box( array(
              'id'           => 'cf_plugins_admin_toolbar',
              'title'        => esc_html__( 'Admin Toolbar', 'cmb2' ),
			  'menu_title'	 =>  'Admin Toolbar',
              'object_types' => array( 'options-page' ),
              'option_key'   => 'cf_plugins_admin_toolbar',
              'parent_slug'  => 'cf_plugins_shortcodes_options',
          ) );
      
	    $secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-admin-settings"></span> '.__('Settings', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'general_title'
        ) );
		
		$secondary_options->add_field( array(
            'name' => __('Always hide the toolbar on the frontend', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'toolbar_frontend_hide',
            'type' => 'checkbox',
        ) );
		
		$secondary_options->add_field( array(
            'name' => __('Hide Help Tab everywhere', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'help_tab_hide',
            'type' => 'checkbox',
        ) );
		
			$secondary_options->add_field( array(
            'name' => __('Hide Screen Opitons everywhere', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'screen_options_hide',
            'type' => 'checkbox',
        ) );
		
		
			    $secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-admin-settings"></span> '.__('WP Logo Replacement', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'logo_title'
        ) );
		
		
		$secondary_options->add_field( array(
            'name' => __('Hide', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'toolbar_wplogo_hide',
            'type' => 'checkbox',
        ) );
		
			$secondary_options->add_field( array(
            'name' => __('Add your own Logo', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'toolbar_wplogo_add_your_own',
            'type' => 'checkbox',
        ) );
		
		$secondary_options->add_field( array(
          'name'             => __('Logo Size', 'cubicfusion-admin-enhancer' ),     
          'id'               => 'toolbar_wplogo_add_your_own_size',
          'type'             => 'radio',
          'show_option_none' => false,
          'options'          => array(
              '32_32' => __( '16x32', 'cmb2' ),
              '160_32'   => __( '160x32', 'cmb2' ),
              '160_60'     => __( '160x60', 'cmb2' ),
              '160_160'     => __( '160x160', 'cmb2' ),
          ),
      ) );
		
			$secondary_options->add_field( array(
            'name' => 'Custom Height',            
            'default' => '',
            'id' => 'toolbar_wplogo_add_your_own_height',
            'type' => 'text'
        ) );
		
		$secondary_options->add_field( array(
          'name'    => 'BG Color',
          'id'      => 'toolbar_wplogo_add_your_own_bgcolor',
          'type'    => 'colorpicker',
          'default' => '#18bc9c',
          // 'options' => array(
          // 	'alpha' => true, // Make this a rgba color picker.
          // ),
      ) );
		$secondary_options->add_field( array(
            'name' => 'Image',            
            'default' => '',
            'id' => 'toolbar_wplogo_add_your_own_img',
            'type' => 'file',
			'preview_size' => 'medium',
        ) );
		$secondary_options->add_field( array(
            'name' => __('Pure Text', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'toolbar_wplogo_add_your_own_text',
            'type' => 'text',
        ) );
		
		$secondary_options->add_field( array(
          'name'    => 'Text Color',
          'id'      => 'toolbar_wplogo_add_your_own_txtcolor',
          'type'    => 'colorpicker',
          'default' => '#ffffff',
          // 'options' => array(
          // 	'alpha' => true, // Make this a rgba color picker.
          // ),
      ) );
		
		
			$secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-admin-settings"></span> '.__('Footer Options', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'footer_options'
        ) );
		
		$secondary_options->add_field( array(
            'name' => __('Hide footer text', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'footer_text_hide',
            'type' => 'checkbox',
        ) );
		
		$secondary_options->add_field( array(
            'name' => 'Change footer text. (Leftside of the footer)',            
            'default' => '',
            'id' => 'footer_text_change',
            'type' => 'text'
        ) );
		
			$secondary_options->add_field( array(
            'name' => __('Hide footer version', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'footer_version_hide',
            'type' => 'checkbox',
        ) );
		
			$secondary_options->add_field( array(
            'name' => 'Change version text (Rightside of the footer)',            
            'default' => '',
            'id' => 'footer_version_change',
            'type' => 'text'
        ) );
		
			$secondary_options->add_field( array(
            'name' => 'Add to footer div. (HTML allowed)',            
            'default' => '',
            'id' => 'footer_in_change',
            'type' => 'textarea_code'
        ) );
		
		$secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-admin-settings"></span> '.__('Hide Toolbar Options', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'general_options'
        ) );
		
		$nodes = get_option("cubicfusion_admin_toolbar_nodes");
		
		if(!empty($nodes)){
		
			foreach($nodes as $node){
            
                if(empty($node->parent) or in_array($node->id, array("my-account")) ){

                    $secondary_options->add_field( array(
                      'name' => __("<a class='toolbar_node' title='".strip_tags($node->title)."'>".$node->id."</a>", 'cubicfusion-admin-enhancer' ),
                      //'desc' => 'field description (optional)',
                      'id'   =>  "admin_toolbar_node_".$node->id.'_disabled',
                      'type' => 'checkbox',
                  ) ); 
                 }		
          }
		}
		
	
	}
	
	function handleSettings(){
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_frontend_hide' ) !== false ){
			
			add_action('after_setup_theme', function () {       
              show_admin_bar(false);          
            });
		}
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'toolbar_wplogo_hide' ) !== false ){
			
          add_action( 'wp_before_admin_bar_render', 		function () {
              global $wp_admin_bar;
              $wp_admin_bar->remove_menu( 'wp-logo' );
          }, 0 );
		}
		
	
		
		add_action( 'wp_before_admin_bar_render',function(){
			global $wp_admin_bar;
			
			$nodes = $wp_admin_bar->get_nodes();
			
			add_option('cubicfusion_admin_toolbar_nodes', $nodes );	
			
			foreach($nodes as $node){
				if( !empty(\CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', "admin_toolbar_node_".$node->id.'_disabled' ) ) && \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', "admin_toolbar_node_".$node->id.'_disabled' ) !== false ){
				 	$wp_admin_bar->remove_menu( $node->id );
				}
			}
		});
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'help_tab_hide' ) !== false ){
          add_filter('contextual_help_list',function (){
              global $current_screen;
              $current_screen->remove_help_tabs();
          });
		}
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'screen_options_hide' ) !== false ){
          add_filter('screen_options_show_screen', '__return_false');
		}
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_text_hide' ) !== false ){
			add_filter( 'admin_footer_text', '__return_empty_string', 11 ); 
		}
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_version_hide' ) !== false ){
			add_filter( 'update_footer', '__return_empty_string', 11 );
		}
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_text_change' ) !== false ){
			add_filter( 'admin_footer_text', function(){
				echo \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_text_change' ) ;
			}, 11 ); 
		}
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_version_change' ) !== false ){
			add_filter( 'update_footer', function(){
				echo \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_version_change' ) ;
			}, 11 ); 
		}
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_in_change' ) !== false ){
			add_filter( 'in_admin_footer', function(){
				echo \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_admin_toolbar', 'footer_in_change' ) ;
			}, 12 ); 
		}
		
	}
	
}