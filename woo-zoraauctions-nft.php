<?php

	/**
	 * Plugin Name: NFT Zora Woocommerce
	 * Plugin URI: https://artbroods.com
	 * Description: Woocommerce plugin for NFT auctions using zora
	 * Version: 1.0.0
	 * Author: ArtBroods
	 * Author URI: https://artbroods.com
	 * Text domain: artbroods_zora_auctions
	 */


	define( 'ABSZAN_TEXT_DOMAIN', 'artbroods_zora_auctions');
	define( 'ABSZAN_VERSION', '1.0.0' ); // WRCS: DEFINED_VERSION.
	define( 'ABSZAN_FILE', __FILE__ );
	define( 'ABSZAN_URL', plugins_url( '', __FILE__ ) );
	define( 'ABSZAN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'ABSZAN_INC_DIR', ABSZAN_DIR . 'includes' );
	define( 'ABSZAN_INC_URL', ABSZAN_URL . '/includes' );

	define( 'ABSZAN_ASSETS_URL', ABSZAN_URL . '/assets' );

	define( 'ABSZAN_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

	// echo ABSZAN_ASSETS_URL;

	require_once ABSZAN_DIR . '/init.php';