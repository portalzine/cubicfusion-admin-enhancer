<?php
namespace CUBICFUSION\Plugins;

if(!defined('ABSPATH')) { exit; }

\CUBICFUSION\Core\CUBIC_HOOKS::set('MODULE', 'cf_plugins_dashboard_gutenberg', (object) array(
    "name" 			=> "Shortcodes",
    "short" 		=> "Module: Dashboard Welcome Gutenberg",
    "version" 		=> "0.2",
	"updated"		=> "07.11.2020",
    "description" 	=> __("<p>This Addon allows you to build a Dashboard with Gutenberg. You can create a new Dashboard under 'Dashboard Templates' and set a default template below. Will be extending this to allow different templates for different roles / groups. A Gutenberg block is included to integrate the current dashboard widgets and tweak them.</p>", 'cubicfusion-admin-enhancer' ),
	"external-links"=> array(),
    "url" 			=> "",
    "documentation" => "",
	"style"			=> 1 //'.mo{color: red;}'
));

class Dashboard_Gutenberg {
	
	
	function init(){		
	
   
  		add_action('cmb2_admin_init', array($this,'register_my_admin_page'), 80);		
		add_action('admin_enqueue_scripts', array($this, 'admin_assets'));	
		add_action( 'cmb2_before_options-page_form_cf_plugins_dashboard_gutenberg', 'CUBICFUSION\Core\GUI::cmb2_before_form', 10, 2 );
		add_action( 'cmb2_after_options-page_form_cf_plugins_dashboard_gutenberg', 'CUBICFUSION\Core\GUI::cmb2_after_form', 10, 2 );	
		
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_dashboard_gutenberg', 'dashboard_activated' ) !== false ){
		
          remove_action( 'welcome_panel', 'wp_welcome_panel' );
          add_action( 'welcome_panel', array( $this, 'welcome_panel' ) );

          if ( ! current_user_can( 'edit_theme_options' ) ) {
              add_action( 'admin_notices', array( $this, 'welcome_panel' ) );
          }
			
		}
		add_action('init', array($this, 'registerPostType') );
		
		add_filter( 'block_categories', array($this, 'registerBlockCategory'), 10, 2);
		add_action('init', array($this, 'registerBlocks') );
		
		add_action( 'admin_init', function(){
			add_submenu_page( 'cf_plugins_shortcodes_options', 'Dashboard Templates', 'Dashboard Templates', 'manage_options', 'edit.php?post_type=cf_dashboard' );
		},99);
		
		add_action('admin_head', array($this, 'customCSS'));
		
		add_action( 'all_admin_notices', function () {

            $screen = get_current_screen();		

            if($screen->post_type !=='cf_dashboard'){
                return;
            }
			
            $data = \CUBICFUSION\Core\CUBIC_HOOKS::get('MODULE', 'cf_plugins_dashboard_gutenberg');
            echo "<div class='wrap'>";
			
			
            	\CUBICFUSION\Core\GUI::buildHeader('Dashboard Gutenberg', $data);
            echo "</div>";

        } );
		
	}
			
	
	function customCSS(){	
		
		echo '<style>'; 		
			echo  \CUBICFUSION\Core\GUI::compileCSS("dashboard.gutenberg.custom_css", ".cf-gutenberg-wrapper{".get_post_meta( cmb2_get_option( 'cf_plugins_dashboard_gutenberg', 'dashboard_main_template' ), '_template_css_extra', true )."}" , true);
		
  		echo'</style>';
	}
	
	
	function admin_assets( $hook ) {
 	
		if ( 'index.php' != $hook ) {
        	return;
		}
        
		if( \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_dashboard_gutenberg', 'dashboard_activated' ) == false ){
            return;
        }
		
		wp_enqueue_style('cf-dashboard_gutenberg-core', get_site_url().'/wp-includes/css/dist/block-library/style.min.css');
		wp_enqueue_style('cf-dashboard_gutenberg-theme', get_site_url().'/wp-includes/css/dist/block-library/theme.min.css');	
		
		$styles = cmb2_get_option( 'cf_plugins_dashboard_gutenberg', 'dashboard_loaded_styles' );
		
		if($styles){
			foreach( $styles as $key=> $style_load){
				wp_enqueue_style("cf-dashboard_gutenberg".basename($style_load), $style_load);
			}
		}
			
		
		wp_enqueue_style('cf-dashboard_gutenberg-styles', plugin_dir_url( dirname(__FILE__) ).'assets/css/dashboard.gutenberg.css');
    	wp_enqueue_script( 'cf-dashboard_gutenberg-script', plugin_dir_url( dirname(__FILE__) ).'assets/js/dashboard.gutenberg.min.js', array(), '1.0' );
		
	}
	
	public static
	function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	function registerBlockCategory( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'cf-blocks',
				'title' => __( 'cubicFUSION', 'mario-blocks' ),
			),
		)
	);
}

	
	function registerBlocks(){
		
		if (!function_exists('register_block_type')) {
			return;
		}
		
		$dir = dirname(__FILE__);

		$index_js = 'admin.widgets.js';
	
        wp_register_script(
            'cf-admin-widgets',
            plugins_url("../assets/js/".$index_js, __FILE__),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components'
            ),
            filemtime(CUBIC_PATH."/assets/js/".$index_js)
        );

        wp_register_style(
            'cf-admin-widgets-editor-css',
            plugins_url( "../assets/css/admin.widgets.css", __FILE__ ),
            array( 'wp-edit-blocks' ),
            filemtime( CUBIC_PATH."/assets/css/admin.widgets.css" )
        );


        wp_register_script( 'cf-admin-widgets-local', plugins_url("../assets/js/admin.widgets.local.js", __FILE__) );
        $save = get_option( 'cubicfusion_cache_widgets' );
 		$translation_array = array();
        
		if(!empty($save)){             
           $short_codes[] = array("value" => "", "label" =>"Choose" ) ;
           
			foreach($save as $widget){
                $short_codes[] = array("value" => $widget['key'], "label" =>strip_tags($widget['name']) ) ;
            }

            $translation_array = array(            
                'my_options' => $short_codes
            );
        }
        
		wp_localize_script( 'cf-admin-widgets-local', 'CF', $translation_array );

		
        wp_enqueue_script( 'cf-admin-widgets-local' );

        register_block_type('cf-blocks/admin-widgets', array(
            'editor_script' => 'cf-admin-widgets',
             'editor_style'    => 'cf-admin-widgets-editor-css',
            'render_callback' => array($this, 'blockHandler'),
            'attributes' => [

                'shortcode' => [
                    'default' => ''
                ],
                'color' => [
                    'default' => ''
                ],
                'textColor' => [
                    'default' => ''
                ],
                'linkColor' => [
                    'default' => ''
                ],
                'className' => [
                    'default' => ''
                ]

            ]
        ));
	}
	
	function blockHandler($atts){		
		
		if(empty($atts['shortcode'])){
			return "Choose Widget!";
		}
	
		$widgets = get_option( 'cubicfusion_cache_widgets' );
		
		ob_start();
			if (is_callable($widgets[$atts['shortcode']]['callback'])) {
		 		call_user_func($widgets[$atts['shortcode']]['callback']);
			}
			$output_string = ob_get_contents();
	
		ob_end_clean();
		
		if(empty(trim(strip_tags($output_string)))){
			return  "<h3>".$widgets[$atts['shortcode']]['name']."</h3> There is no preview available, but the widget will render on the Dashboard itself.";
		}
		
		$construct = "<div class='cf-container";
		
		if(!empty($atts['className'])){
			$construct .= " ".$atts['className'];
		}
		
		$construct .= "' ";
		
		if(!empty($atts['color'])){
			$construct .= " data-bg-color='".$atts['color']."'";
		}
		
		if(!empty($atts['textColor'])){
			$construct .= " data-color='".$atts['textColor']."'";
		}
		
		if(!empty($atts['linkColor'])){
			$construct .= " data-link-color='".$atts['linkColor']."'";
		}
		
		$construct .= ">".$output_string."</div>";
		
		return $construct;
		
	}
	
	function registerPostType(){
	
			
			register_post_type('cf_dashboard',
              array(
                  'labels'      => array(
                    'name' 			=> __('CF Dashboard Templates', 'post type general name'),
                    'singular_name' => __('CF Dashboard Templates', 'post type singular name'),
                    'add_new' 		=> __('Add New', 'post item'),
                    'add_new_item' 	=> __('Add New Item'),
                    'edit_item' 	=> __('Edit Item'),
                    'new_item' 		=> __('New Item'),
                    'view_item' 	=> __('View  Item'),
                    'search_items' 	=> __('Search'),
                    'not_found' 	=>  __('Nothing found'),
                    'not_found_in_trash' 	=> __('Nothing found in Trash'),
                    'parent_item_colon' 	=> ''
                  ),
                  'public' => false,
                  'publicly_queryable' => false,
                  'show_ui' => true,			
                  'query_var' => true,
				  'show_in_menu'      => false,
                  'menu_icon' =>  'dashicons-editor-table',
                  'rewrite' => true,
                  'capability_type' => 'page',
                  'hierarchical' => true,
                  'menu_position' => 99,
                  'show_in_rest' => true,
                  'supports' => array('title','editor','thumbnail', "author","revisions", "page-attributes","custom-fields"), 
                  'taxonomies' => array(),
                  'has_archive' => true
                 
              )
          );  	
	}
	
	function welcome_panel(){
				
		$page = get_post( cmb2_get_option( 'cf_plugins_dashboard_gutenberg', 'dashboard_main_template' ) );
		
		?>
		<div class="welcome-panel-content cf-gutenberg-wrapper">
		<?php if (  \CUBICFUSION\Core\Basics::cmb2_get_option( 'cf_plugins_dashboard_gutenberg', 'dashboard_dismissable' ) !== false ) { ?>
        	<a title="<?php _e('Dismiss'); ?>" class="cf-welcome-panel-close" href="<?php echo admin_url('?welcome=0'); ?>"><span class="dashicons dashicons-dismiss"></span></a>
		<?php } ?>
		
		<?php echo do_shortcode(do_blocks($page->post_content)); ?>
		</div>
		<?php
	}
	
	function register_my_admin_page() {
  
  		\CUBICFUSION\Core\Basics::admin_can_edit();
		
		$cmb = new_cmb2_box( array(
            'id'            => 'extra_options',
            'title'         => __( 'Extra Options', 'cmb2' ),
            'object_types'  => array( 'cf_dashboard', ), // Post type
            'context'       => 'normal',
            'priority'      => 'high'	
		) );
		
				
		$cmb->add_field( array(
            'name' => 'Extra Styles',            
            'default' => '',
            'id' => '_template_css_extra',
            'type' => 'textarea',
			'before_field' 	=> '.cf-gutenberg-wrapper{<br>',
			'after_field' 	=> '<br>}',
        ) );

        $secondary_options = new_cmb2_box( array(
              'id'           => 'cf_plugins_dashboard_gutenberg',
              'title'        => esc_html__( 'Dashboard Gutenberg', 'cmb2' ),
			  'menu_title'	 =>  'Dashboard Gutenberg',
              'object_types' => array( 'options-page' ),
              'option_key'   => 'cf_plugins_dashboard_gutenberg',
              'parent_slug'  => 'cf_plugins_shortcodes_options',
          ) );
      
	    $secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-admin-settings"></span> '.__('Settings', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'general_title'
        ) );
		
		$args = array(
          'numberposts' => -1,
          'post_type'   => 'cf_dashboard'
        );

        $templates= get_posts( $args );
		
		$options = [];
		
		foreach($templates as $plate){
			$options[$plate->ID] = $plate->post_title;
		}		
		
		$secondary_options->add_field( array(
            'name' => __('Activated', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'dashboard_activated',
            'type' => 'checkbox',
        ) );
		
		$secondary_options->add_field( array(
            'name' => __('Is dismissable?', 'cubicfusion-admin-enhancer' ),             
            'id'   =>  'dashboard_dismissable',
            'type' => 'checkbox',
        ) );
		
		$secondary_options->add_field( array(
            'name'             => 'Choose Template',
            'desc'             => 'Select a template or <a href="/wp-admin/edit.php?post_type=cf_dashboard">create one</a>.',
            'id'               => 'dashboard_main_template',
            'type'             => 'select',
            'show_option_none' => true,          
            'options'          => $options,
        ) );
		
		$secondary_options->add_field( array(
            'name' => '<span class="dashicons dashicons-buddicons-topics"></span> '.__('Styles', 'cubicfusion-admin-enhancer'),          
            'type' => 'title',
            'id'   => 'general_style'
              ) );
		
		$secondary_options->add_field( array(
            'name'    => 'Default Styles',
            'desc'    => 'Default Gutenberg styles:<br>'.get_site_url().'/wp-includes/css/dist/block-library/style.min.css<br>'.get_site_url().'/wp-includes/css/dist/block-library/theme.min.css<br><br>'.' If you use none core WordPress blocks you need to add the styles here.',
            // 'default' => 'standard value (optional)',
            'id'      => 'dashboard_loaded_styles',
            'type'    => 'text',
			'repeatable' => true,		
			//'default_cb' => array($this, 'set_to_today'),
			'text' => array(
				'add_row_text' => 'Add Another Stylesheet',				
			),
        ) );
		
		$secondary_options->add_field( array(
            'name' 		=> 'Extra Styles',            
            'default' 	=> '',
            'id' 		=> 'dashboard_css_extra',
            'type' 		=> 'textarea_code',
			'before_field' 	=> '.cf-gutenberg-wrapper{',
			'after_field' 	=> '}',
        ) );	
		
		
	
	}
	
	function set_to_today( $field_args, $field ) {
		
	
			$my_field1 = get_site_url().'/wp-includes/css/dist/block-library/style.min.css';
			$my_field2 = get_site_url().'/wp-includes/css/dist/block-library/theme.min.css';
	 		$data = array(  $my_field1, $my_field2  );
			return $data;
	
		
	}
	
	
}