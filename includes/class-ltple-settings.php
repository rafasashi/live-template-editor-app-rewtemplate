<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class LTPLE_App_Boilerplate_Class_Settings {

	/**
	 * The single instance of LTPLE_App_Boilerplate_Class_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		
		$this->parent = $parent;
		
		$this->plugin 		 	= new stdClass();
		$this->plugin->slug  	= 'live-template-editor-app-boilerplate-slug';
		
		add_action('ltple_plugin_settings', array($this, 'plugin_info' ) );
		
		add_action('ltple_plugin_settings', array($this, 'settings_fields' ) );
		
		add_action( 'ltple_admin_menu' , array( $this, 'add_menu_items' ) );	
	}
	
	public function plugin_info(){
		
		$this->parent->settings->addons['app-boilerplate-slug-plugin'] = array(
			
			'title' 		=> 'App Boilerplate_Class Plugin',
			'addon_link' 	=> 'https://github.com/rafasashi/live-template-editor-app-boilerplate-slug',
			'addon_name' 	=> 'live-template-editor-app-boilerplate-slug',
			'source_url' 	=> 'https://github.com/rafasashi/live-template-editor-app-boilerplate-slug/archive/master.zip',
			'description'	=> 'App Name API integrator for Live Template Editor',
			'author' 		=> 'Rafasashi',
			'author_link' 	=> 'https://profiles.wordpress.org/rafasashi/',
		);		
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	public function settings_fields () {

	}
	
	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_items () {
		
		//add menu in apps dashboard
		/*
		add_submenu_page(
			'live-template-editor-client',
			__( 'App App Name', $this->plugin->slug ),
			__( 'App App Name', $this->plugin->slug ),
			'edit_pages',
			'edit.php?post_type=post'
		);
		*/
	}
}
