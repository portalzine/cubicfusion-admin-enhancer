<?php

namespace CUBICFUSION\Core;

if(!defined('ABSPATH')) { exit; }

class Basics{
	
	
	protected 			$plugin_slug;
	protected static 	$instance = null;
	public 				$page_data;


	public

	function __construct() {

		$this->templates = array();
		$this->page_data = get_page_by_path( $_SERVER[ 'REQUEST_URI' ], OBJECT, 'page' );
		load_plugin_textdomain( 'cubicfusion-admin-widgets', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		
		$this->traitInit();	
	}
	
	function get_editable_roles() {
		global $wp_roles;

    	$all_roles = $wp_roles->roles;
    	$editable_roles = apply_filters('editable_roles', $all_roles);

    	return $editable_roles;
	}
	
	static
	function cmb2_get_option($from, $key = '', $default = false ) {
      if ( function_exists( 'cmb2_get_option' ) ) {
          // Use cmb2_get_option as it passes through some key filters.
          return cmb2_get_option( $from, $key, $default );
      }

      // Fallback to get_option if CMB2 is not loaded yet.
      $opts = get_option( $from, $default );

      $val = $default;

      if ( 'all' == $key ) {
          $val = $opts;
      } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
          $val = $opts[ $key ];
      }

      return $val;
  }
	
	public static function admin_can_edit() {
		if ( ! is_admin() ) {
			return;
		}	
		if ( !current_user_can( 'manage_options' ) )
			return;
	}
	
	public static

	function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	function is_serialized( $data ) {
		return ( is_string( $data ) && preg_match( "#^((N;)|((a|O|s):[0-9]+:.*[;}])|((b|i|d):[0-9.E-]+;))$#um", $data ) );
	}

	function unserializeString( $string ) {
		if ( $this->is_serialized( $string ) ) return unserialize( $string );

		return $string;
	}


	function isJson( $string ) {
		json_encode( $string );
		return ( json_last_error() == JSON_ERROR_NONE );
	}


	function array_key_exists_wildcard( $array, $search, $return = '' ) {
		$search = str_replace( '\*', '.*?', preg_quote( $search, '/' ) );
		$result = preg_grep( '/^' . $search . '$/i', array_keys( $array ) );
		if ( $return == 'key-value' )
			return array_intersect_key( $array, array_flip( $result ) );
		return $result;
	}

	function array_value_exists_wildcard( $array, $search, $return = '' ) {
		$search = str_replace( '\*', '.*?', preg_quote( $search, '/' ) );
		$result = preg_grep( '/^' . $search . '$/i', array_values( $array ) );
		if ( $return == 'key-value' )
			return array_intersect( $array, $result );
		return $result;
	}

	/* Clean WP-Meta */
	function array_push_assoc( $array, $key, $value ) {
		if ( preg_match( '!{i!', $value, $match ) )$value = unserialize( $value );
		$array[ $key ] = $value;
		return $array;
	}

	function filter_gpm( $array ) {
		$mk = array();
		foreach ( $array as $k => $v ) {
			if ( is_array( $v ) && count( $v ) == 1 ) {
				$mk = $this->array_push_assoc( $mk, $k, $v[ 0 ] );
			} else {
				$mk = $this->array_push_assoc( $mk, $k, $v );
			}
		}
		return $mk;
	}

	/* TRAITS */

	function traitInit() {

		foreach ( class_uses( get_called_class() ) as $trait ) {
			$init = $trait . "_init";
			$init = 	stripslashes (str_replace(__NAMESPACE__,"", $init));
			
			if ( method_exists( get_called_class(), $init ) ) {
               // echo $init;
                
				$this->$init();
			}
		}
	}

	function array_insert( & $array, $element, $position = null ) {
		if ( count( $array ) == 0 ) {
			$array[] = $element;
		} elseif ( is_numeric( $position ) && $position < 0 ) {
			if ( ( count( $array ) + position ) < 0 ) {
				$array = array_insert( $array, $element, 0 );
			} else {
				$array[ count( $array ) + $position ] = $element;
			}
		}
		elseif ( is_numeric( $position ) && isset( $array[ $position ] ) ) {
			$part1 = array_slice( $array, 0, $position, true );
			$part2 = array_slice( $array, $position, null, true );
			$array = array_merge( $part1, array( $position => $element ), $part2 );
			foreach ( $array as $key => $item ) {
				if ( is_null( $item ) ) {
					unset( $array[ $key ] );
				}
			}
		}
		elseif ( is_null( $position ) ) {
			$array[] = $element;
		}
		elseif ( !isset( $array[ $position ] ) ) {
			$array[ $position ] = $element;
		}
		$array = array_merge( $array );
		return $array;
	}
	
	

}

  if(!class_exists("CUBIC_HOOKS")){
   class CUBIC_HOOKS {
    static $_this = array();


     function __construct(){
        self::$_this = $this;
    }

    static function &getInstance(){
        return self::$_this;
    }

    static function set($type,$key, $value){
        self::$_this[$type][$key] = $value;
    }
	
	static function add($type,$key, $value){
        self::$_this[$type][$key][] = $value;
    }

    static function &get($type, $key){
        return self::$_this[$type][$key];
    }
	static function &getType($type){
        return self::$_this[$type];
    }
	static function &all(){
        return self::$_this;
    }
}
class CUBIC_STATUS {
    static $_this = array();


    function __construct(){
        self::$_this = $this;
    }

    static function &getInstance(){
        return self::$_this;
    }

    static function set($type,$key, $value){
        self::$_this[$type][$key][] = $value;
    }

    static function &get($type,$key){
        return self::$_this[$type][$key];
    }
	static function &getType($type){
        return self::$_this[$type];
    }
	static function &all(){
        return self::$_this;
    }
}
	}