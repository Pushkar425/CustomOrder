<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.author.com
 * @since      1.0.0
 *
 * @package    Customorder
 * @subpackage Customorder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Customorder
 * @subpackage Customorder/admin
 * @author     Pushkar <pushkar@gmail.com>
 */
class Customorder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Customorder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Customorder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/customorder-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Customorder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Customorder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/customorder-admin.js', array( 'jquery' ), $this->version, false );

	}

	function my_menu_page(){
		add_menu_page('Custom Order','Custom Order','manage_options','order',array($this,'ordering'),'dashicona-upload',53);
	}

	function ordering(){
		?>
			<div>
				<form action="" method="POST" onsubmit="ordering()" enctype="multipart/form-data">
					<label>file: <label>
					<input type="file" name="file">
					<input type="submit" name="submit">
				</form>
			</div>


		<?php
		global $woocommerce;
		if(isset($_FILES['file'])){
			$dir = wp_upload_dir();
			$path = $dir['basedir'].'/'.$_FILES['file']['name'];
			//echo "getting";
			$fp = fopen($path,'r');
			$content = html_entity_decode(file_get_contents($path));
			$file = json_decode($content,true);
			
				foreach($file as $key => $value){
					if($key == "OrderArray"){
						foreach($value as $k => $v){
							foreach($v as $dat => $info){
								$data1=[
									'payment_method'=>$info['PaymentMethods'][0],
									'payment_method_title'=>$info['PaymentMethods'][0],
									'billing'=>[
										'first_name'=>$info['TransactionArray']['Transaction'][0]['Buyer']['UserFirstName'],
										'last_name'=>$info['TransactionArray']['Transaction'][0]['Buyer']['UserLastName'],
										'address_1'=>$info['ShippingAddress']['Street1'],
										'address_2'=>$info['ShippingAddress']['Street2'],
										'state'=>$info['ShippingAddress']['StateOrProvince'],
										'country'=>$info['ShippingAddress']['CountryName'],
										'city'=>$info['ShippingAddress']['CityName'],
										'postcode'=>$info['ShippingAddress']['PostalCode'],
										'email'=>$info['TransactionArray']['Transaction'][0]['Buyer']['Email'],
										'phone'=>$info['ShippingAddress']['Phone'] 
									],
									'shipping'=>[
										'first_name' => $info['TransactionArray']['Transaction'][0]['Buyer']['UserFirstName'],
										'last_name' => $info['TransactionArray']['Transaction'][0]['Buyer']['UserLastName'],
										'address_1' => $info['ShippingAddress']['Street1'],
										'address_2' => '',
										'city' => $info['ShippingAddress']['CityName'],
										'state' => $info['ShippingAddress']['StateOrProvince'],
										'postcode' => $info['ShippingAddress']['PostalCode'],
										'country' => $info['ShippingAddress']['CountryName']
									],
									'shipping_lines'=>[
										'method_id'=>'flat_rate',
										'method_title'=>'Flat Rate',
										'total'=>$info['Total']['value']
									]
			
								];
							}
						}
					}
				
				
			}
			
		
			fclose($fp);
			//die($data1['billing']);
			$order = wc_create_order();
			$order->add_product(wc_get_product( '13' ), 1 );
			$order->set_address($data1['billing'],'billing');
			$order->set_address($data1['shipping'],'shipping');
			$order->calculate_totals();
			$order->update_status('Completed','Imported',TRUE);
		}

	}

	

}
