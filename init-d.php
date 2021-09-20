<?php

	if ( ! defined( 'ABSPATH' ) ) {
	    return;
	}

	add_action( 'init', 'register_nftauction_product_type' );

	function register_nftauction_product_type(){
		class WC_Product_Demo extends WC_Product{
			public function __construct( $product ) {
		        $this->product_type = 'nftauction';
				parent::__construct( $product );
		    }
		}
	}

	class AB_Product_Type {

		public function __construct(){
			
			// add_action( 'woocommerce_loaded', array( $this, 'load_plugin' ) );
			add_filter( 'product_type_selector', array( $this, 'add_type' ) );
			// register_activation_hook( __FILE__, array( $this, 'install' ) );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_tab' ), 50 );
        	add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_tab_content' ) );

        	add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        	add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_settings' ) );

        	add_action( 'woocommerce_single_product_summary', array( $this, 'nftauction_product_front' ) );

		}

		public function enqueue_scripts(){

			wp_enqueue_script( 'abszan_web3_min', ABSZAN_ASSETS_URL . '/js/web3.min.js', array(), ABSZAN_TEXT_DOMAIN );
			wp_enqueue_script( 'abszan_abis', ABSZAN_ASSETS_URL . '/js/abis.js', array(), ABSZAN_TEXT_DOMAIN );
			wp_enqueue_script( 'abszan_data', ABSZAN_ASSETS_URL . '/js/data.js', array(), ABSZAN_TEXT_DOMAIN );
			wp_enqueue_script( 'abszan_block_script', ABSZAN_ASSETS_URL . '/js/Block.js', array(), ABSZAN_TEXT_DOMAIN );

		}

		/*
	     * Load WC Dependencies
		 *
		 * @return void
		 */
		public function load_plugin() {
		    require_once ABSZAN_INC_DIR . '/class-woocommerce-product-nft.php';
		}

		/**
	     * NFT Auction Type
	     *
	     * @param array $types
	     * @return void
	     */
	    public function add_type( $types ) {
	        $types['nftauction'] = __( 'NFT Auction', ABSZAN_TEXT_DOMAIN );
	       
	        return $types;
	    }

	    /**
	     * Installing on activation
	     *
	     * @return void
	     */
	    public function install() {
	        // If there is no nftauction product type taxonomy, add it.
	        if ( ! get_term_by( 'slug', 'nftauction', 'product_type' ) ) {
		  		wp_insert_term( 'nftauction', 'product_type' );
	        }
	    }


	    /**
	     * Add Experience Product Tab.
	     *
	     * @param array $tabs
	     *
	     * @return mixed
	     */
	    public function add_product_tab( $original_tabs ) {

	      	$new_tab['nftauction'] = array(
				'label'    => __( 'NFT Auction', ABSZAN_TEXT_DOMAIN ),
				'target' => 'nftauction_product_options',
				'class'  => 'show_if_nftauction',
			);

			$insert_at_position = 1; // This can be changed
			$tabs = array_slice( $original_tabs, 0, $insert_at_position, true ); // First part of original tabs
			$tabs = array_merge( $tabs, $new_tab ); // Add new
			$tabs = array_merge( $tabs, array_slice( $original_tabs, $insert_at_position, null, true ) ); // Glue the second part of original

	        
	      	return $tabs;
	    }

	    /**
	     * Add Content to Product Tab
	     */
	    public function add_product_tab_content() {
			global $product_object;
			?>
			<div id='nftauction_product_options' class='panel woocommerce_options_panel hidden'>
				<div class='options_group'>
					<?php

						woocommerce_wp_text_input(
							array(
								'id'          => '_token_id',
								'label'       => __( 'Token ID', ABSZAN_TEXT_DOMAIN ),
								'description' => __( '(Number)', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_token_id', true ),
								'default'     => '',
								'placeholder' => '',
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_token_contract',
								'label'       => __( 'Token Contract', ABSZAN_TEXT_DOMAIN ),
								'description' => __( '(Contract Address)', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_token_contract', true ),
								'default'     => '',
								'placeholder' => '',
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_auction_duration',
								'type'		  => 'datetime-local',
								'label'       => __( 'Auction Duration', ABSZAN_TEXT_DOMAIN ),
								'description' => __( '(The length of time, in seconds, that the auction should run for once the reserve price is hit.)', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_auction_duration', true ),
								'default'     => '',
								'placeholder' => '',
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_reserve_price',
								'label'       => __( 'Reserve Price', ABSZAN_TEXT_DOMAIN ),
								'description' => __( '(The minimum price for the first bid, starting the auction)', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_reserve_price'),
								'default'     => '',
								'placeholder' => '',
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_curator',
								'label'       => __( 'Curator', ABSZAN_TEXT_DOMAIN ),
								'description' => __( '(Ethereum Address Optional)', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_curator'),
								'default'     => '',
								'placeholder' => '',
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_curator_fee_percent',
								'label'       => __( 'Curator Free Percentage', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_curator_fee_percent'),
								'default'     => '',
								'placeholder' => '',
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_auction_currency',
								'label'       => __( 'Auction Currency', ABSZAN_TEXT_DOMAIN ),
								'description' => __( '(The currency to perform this auction in, or 0x0 for ETH)', ABSZAN_TEXT_DOMAIN ),
								'value'       => $product_object->get_meta( '_auction_currency'),
								'default'     => '',
								'placeholder' => '',
							)
						);
					?>

					<a href="#" id="connect-btn">Create Auction Contract</a>
					<a href="#" id="approve">Approve</a>
          			<a href="#" id="auction-create" style="display:none;">Create Auction Contract</a>
				</div>
			</div>
			<?php
	    }

	    public function save_product_settings( $post_id ){
	    	
	    	$_token_id = isset( $_POST['_token_id'] ) ? sanitize_text_field( $_POST['_token_id'] ) : '';
	    	$_token_contract = isset( $_POST['_token_contract'] ) ? sanitize_text_field( $_POST['_token_contract'] ) : '';
	    	$_auction_duration = isset( $_POST['_auction_duration'] ) ? sanitize_text_field( $_POST['_auction_duration'] ) : '';
	    	$_reserve_price = isset( $_POST['_reserve_price'] ) ? sanitize_text_field( $_POST['_reserve_price'] ) : '';
	    	$_curator = isset( $_POST['_curator'] ) ? sanitize_text_field( $_POST['_curator'] ) : '';
	    	$_curator_fee_percent = isset( $_POST['_curator_fee_percent'] ) ? sanitize_text_field( $_POST['_curator_fee_percent'] ) : '';
	    	$_auction_currency = isset( $_POST['_auction_currency'] ) ? sanitize_text_field( $_POST['_auction_currency'] ) : '';

	    	update_post_meta( $post_id, '_token_id', $_token_id );
	    	update_post_meta( $post_id, '_token_contract', $_token_contract );
	    	update_post_meta( $post_id, '_auction_duration', $_auction_duration );
	    	update_post_meta( $post_id, '_reserve_price', $_reserve_price );
	    	update_post_meta( $post_id, '_curator', $_curator );
	    	update_post_meta( $post_id, '_curator_fee_percent', $_curator_fee_percent );
	    	update_post_meta( $post_id, '_auction_currency', $_auction_currency );
	    }


	    public function nftauction_product_front(){
	    	global $product;
	    	echo $product->get_type();
	    	if( 'nftauction' == $product->get_type() ){
	    		echo 'Found NFT Auction Product';
	    	}
	    }
	}


	new AB_Product_Type();