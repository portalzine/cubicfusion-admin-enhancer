<?php
namespace CUBICFUSION\Core;

	require_once($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/cubicfusion-admin-enhancer/inc/scssphp/scss.inc.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/cubicfusion-admin-enhancer/inc/markdown/Parsedown.php");
	use \ScssPhp\ScssPhp\Compiler as SCSSCompiler;


if(!defined('ABSPATH')) { exit; }

\CUBICFUSION\Core\CUBIC_HOOKS::set('MODULE', 'cf_plugins_shortcodes_options', (object) array(
    "name" 			=> "Main",
    "short" 		=> "Core",
    "version" 		=> "0.2",
	"updated"		=> "23.05.2020",
    "description" 	=> __("<p><strong>Admin Enhancer</strong> is a work in progress.</p><p>I am using this WordPress plugin to centralise things I love & need, when sending out a finished website or project.</p><p>These tools are completely free and will always stay free. Check the <strong class='show_change'>Changelog</strong> for the latest changes. </p>", 'cubicfusion-admin-enhancer' ),
    "url" 			=> "",
    "documentation" => "",
	"changelog"		=> "CHANGELOG.txt"
));

class GUI{	
	
	function __construct() {
		add_action('cmb2_admin_init', array($this,'register_my_admin_page'));	
		add_action( 'cmb2_before_options-page_form_cf_plugins_shortcodes', array($this,'cmb2_before_form'), 10, 2 );
		add_action( 'cmb2_after_options-page_form_cf_plugins_shortcodes', array($this,'cmb2_after_form'), 10, 2 );
		add_action('admin_enqueue_scripts', array($this, 'admin_assets'));	
		add_action('admin_head', array($this, 'customCSS'));
		
	}
	
	static
	function readMarkdown($file){
		
		$Parsedown = new \Parsedown();
		
		$file = esc_html(file_get_contents($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/cubicfusion-admin-enhancer/".$file) ) ;
		 
		return $Parsedown->text($file);
	}
	
	static
	function compileCSS($id, $css, $echo =false, $importPath="", $formatter = "ScssPhp\ScssPhp\Formatter\Compressed", $lineNumbers = false){ 
		
			$upload 		=  wp_upload_dir();
			$var_id 		= md5($id);
			$cache_dir 		= CUBIC_ROOT."wp-content/uploads/cubic-cache/css/";
			$cached 		= $id."_".$var_id.".css";
			$cache_file		= $cache_dir.$id."_".$var_id.".css";
				
			 $scss = new SCSSCompiler();
			
			if($lineNumbers == true){
				$scss->setLineNumberStyle(SCSSCompiler::LINE_COMMENTS);
			}
			
			if($importPath != ""){
		
				$scss->setImportPaths(array(CUBIC_ROOT.$path)); 		
			}
		
			$scss->setFormatter($formatter); 

		
			$data= $scss->compile($css);
			
			if($echo == true){
				return $data;
			}
        	

		if(!is_dir($cache_dir )) wp_mkdir_p($cache_dir );
		
		if( ! file_exists( $cache_file	 ) )
            {
                touch( $cache_file	 );
                umask( 0664 );
                chmod( $cache_file	, 0664 );
            }

         if ( is_writable( $cache_file	 ) )
            {
            if ( !$handle = fopen( $cache_file	, "w" ) )
            {

            }

            if ( ! fwrite( $handle, trim( $data ) ) )
            {

            }

            fclose( $handle );		  
        }

        return  $cached ; 
			
	}
	
	static
	function  enqueueSCSS($prefix, $path, $file, $deps = array()){
		
		$scss = self::compileCSS($prefix, $path, $file);
	
	 	wp_register_style( basename($file, ".scss") , "/wp-content/uploads/cubic-cache/css/".$scss, $deps);  
		wp_enqueue_style( basename($file, ".scss") ); 
	}
	
	function customCSS(){
		echo '<style>';
 		
		$modules = \CUBICFUSION\Core\CUBIC_HOOKS::getType('MODULE');
		
		foreach($modules as $key => $module){
		
			if(isset($module->style)){
				$prepare = get_option( $key );
					
				if(isset($prepare['dashboard_css_extra'])){
					echo   \CUBICFUSION\Core\GUI::compileCSS("cf_dashboard_css", ".cf-gutenberg-wrapper{".$prepare['dashboard_css_extra']."}", true);
				}
			}
			if(isset($module->basic_style)){
				echo \CUBICFUSION\Core\GUI::compileCSS("cf_basic_css", $module->basic_style, true);
			}
		}
		
  		echo'</style>';
	}
	
	function admin_assets( $hook ) { 		
						
		wp_enqueue_style('cf-gui-admin-styles', plugin_dir_url( dirname(__FILE__) ).'assets/css/gui.css');
    	wp_enqueue_script( 'cf-gui-admin-script', plugin_dir_url( dirname(__FILE__) ).'assets/js/gui.min.js', array(), '1.0' );
		
	}
	
	static function cmb2_before_form($post_id, $cmb ) {
		
		$title = $cmb->meta_box['title'];
		self::buildHeader($title);		
				
	}
	
	static 
	function cmb2_after_form($post_id, $cmb ) {
		echo '<div class="cf gui_footer">&copy; 2020 portalZINE NMN. <a href="https://portalzine.de" target="_blank">Development meets Creativity!</a>.</div>';
	}	
	
	static public
	function buildHeader($title, $data = ''){
		$screen = get_current_screen();		
	
		global $submenu, $menu, $pagenow;
      
		$current =  substr($screen->base, strpos($screen->base, "page_") + 5); 
		if(!$data){
			$data = \CUBICFUSION\Core\CUBIC_HOOKS::get('MODULE', $current);
		}
		
		if(isset($data->changelog)){
			$changelog =  self::readMarkdown($data->changelog);
		}else{
			
		}$changelog =  self::readMarkdown("CHANGELOG.txt");
		
		echo '<div class="cf gui_header"><img src="'.plugin_dir_url( dirname(__FILE__) ).'assets/images/cubicfusion_logo.png"><div class="title">'.$title.'</div></div>';
		
		
		echo "<nav class='cf nav-tab-wrapper'>";
		foreach($submenu['cf_plugins_shortcodes_options'] as $item){
		
			echo '<a class="nav-tab';
			if($current == $item[2]  or  !empty($screen->post_type) && preg_match("/".$screen->post_type."/",$item['2'], $matches)){
				echo " nav-tab-active";
			}
			if(preg_match("/edit/",$item['2'], $matches)){
				echo' "href="'.str_replace("admin.php?page=","",$item['2']).'">'.$item['0'].'</a>';	
			}else{
				echo' "href="'.admin_url( 'admin.php?page='.$item['2'].'' ).'">'.$item['0'].'</a>';
			}
		}
		echo '<a class="nav-tab social" target="_blank" href="https://portalzine.de"><span class="dashicons dashicons-admin-site-alt3"></span></a>';
		echo '<a class="nav-tab social" target="_blank" href="mailto:ideas@cubicfusion.com"><span class="dashicons dashicons-email"></span></a>';
		echo '<a class="nav-tab social" target="_blank" href="https://twitter.com/pztv"><span class="dashicons dashicons-twitter"></span></a>';
		echo '<a class="nav-tab social" target="_blank" href="https://facebook.com/portalzine"><span class="dashicons dashicons-facebook"></span></a>';
		echo "</nav>";
		
		echo "<div class='cf gui_info'><small><strong><span class='dashicons dashicons-info'> </span> Tell me about it!</strong></small></div><div class='cf gui_about'><img src='".plugin_dir_url( dirname(__FILE__) )."assets/images/profile_alex.jpg'>".$data->description." Enjoy! Alex @ portalZINE ...<br><p>Any suggestions? <a href='https://portalzine.de/contact/' target='_blank'>Feel free to say hi.</a></p>";
		if(!empty($changelog))	{echo"<div class='changelog hidden'><hr>".$changelog."</div>";}
		echo"<div class='version'>";
		if(!empty($changelog))	{echo"<small class='show_change'>Changelog</small> |";}
		echo "<small>Version ".$data->version." | Updated: ".$data->updated."</small></div></div><hr>";
	}
	
	function register_my_admin_page() {
  		
		\CUBICFUSION\Core\Basics::admin_can_edit();
		
        $cmb_options = new_cmb2_box( array(
          'id'           => 'cf_plugins_shortcodes',
          //'title'        => esc_html__( 'General Settings', 'cubicfusion-admin-enhancer' ),
		   'menu_title' =>   'CF Admin Enhancer',
          'object_types' => array( 'options-page' ),
          'option_key'   => 'cf_plugins_shortcodes_options', 
		  'icon_url' 	 => 'dashicons-admin-customizer',
			
        ) );
	}
}
