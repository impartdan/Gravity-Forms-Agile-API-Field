<?php
/*
Plugin Name: Gravity Forms Organization Field
Plugin URI: https://impartcreative.com
Description: Adds a custom field for selecting a school using the Agile API.
Version: 1.1
Author: Impart Creative
Author URI: https://impartcreative.com
*/
defined( 'ABSPATH' ) or die( 'No direct file access allowed!' );

if ( class_exists( 'GFCommon' ) && class_exists('GF_Field')) {

	require_once('lib/agile-api.php');
	require_once('lib/org-field.php');
	GF_Fields::register(new GF_Field_Org());


	add_action( 'admin_head', 'enqueue_form_editor_style' );
	function enqueue_form_editor_style(){
		if ( RGForms::is_gravity_page() ) {
			wp_register_style('org_field', plugins_url('css/org_field.css',__FILE__ ));
			wp_enqueue_style('org_field');
		}
	}
	 
	add_filter( 'gform_noconflict_styles', 'register_style' );
	function register_style( $styles ) {
		$styles[] = 'org_field';
		return $styles;
	}


	// add JS to populate the organization dropdown
	add_action( 'gform_register_init_scripts', 'gform_populate_orgs' );
	function gform_populate_orgs( $form ) {
		wp_register_style('org_field', plugins_url('css/org_field.css',__FILE__ ));
		wp_enqueue_style('org_field');
	}


	// add ajax action to get buildings
	add_action('wp_ajax_nopriv_get_buildings', 'get_buildings'); // for not logged in users
	add_action('wp_ajax_get_buildings', 'get_buildings');
	function get_buildings() {
		$schools = array();
		
		$agile = new AgileAPI();

		// check for a transient with the auth token
		$accessToken = get_transient( 'accessToken' );
		if(!$accessToken){
			$auth = $agile->authenticate();
			set_transient( 'accessToken', $auth->accessToken, $auth->expiresIn );
			$accessToken = $auth->accessToken;
		}

		if(!empty($_POST['zipCode'])){
			$zipCode = filter_var($_POST['zipCode'], FILTER_SANITIZE_NUMBER_INT);
			$schools = $agile->get_buildings($accessToken, $zipCode);
		}

		echo json_encode($schools);
		wp_die(); 
	}

}
