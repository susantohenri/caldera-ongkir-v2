<?php 
/**
 * Plugin Name: Caldera Ongkir v2.0
 * Plugin URI:  https://github.com/susantohenri
 * Description: Add on Caldera Form for calculating ongkos kirim
 * Version:     2.0.0
 * Author:      Henri 081901088918
 * Author URI:  https://bitbucket.org/liemgioktian/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define('CALDERA_ONGKIR_PATH',  plugin_dir_path( __FILE__ ));
define('CALDERA_ONGKIR_URL', plugin_dir_url(__FILE__));

register_activation_hook( __FILE__, function () {
  global $wpdb;
  $queries = array();
  $queries[] = "
    CREATE TABLE `caldera_ongkir_province` (
      `province_id` int(11) NOT NULL,
      `province` varchar(255) NOT NULL DEFAULT ''
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_province`
    ADD PRIMARY KEY (`province_id`)
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_province`
    MODIFY `province_id` int(11) NOT NULL AUTO_INCREMENT
  ";
  $queries[] = "
    CREATE TABLE `caldera_ongkir_city` (
    `city_id` int(11) NOT NULL,
    `city_name` varchar(255) NOT NULL DEFAULT '',
    `type` varchar(255) NOT NULL,
    `province_id` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_city`
    ADD PRIMARY KEY (`city_id`)
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_city`
    MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT
  ";
  $queries[] = "
    CREATE TABLE `caldera_ongkir_subdistrict` (
      `subdistrict_id` int(11) NOT NULL,
      `subdistrict_name` varchar(255) NOT NULL,
      `city_id` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_subdistrict`
    ADD PRIMARY KEY (`subdistrict_id`)
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_subdistrict`
    MODIFY `subdistrict_id` int(11) NOT NULL AUTO_INCREMENT
  ";
  $queries[] = "
    CREATE TABLE `caldera_ongkir_cost` (
      `id` int(11) NOT NULL,
      `origin` int(11) NOT NULL,
      `destination` int(11) NOT NULL,
      `courier` varchar(255) NOT NULL,
      `service` varchar(255) NOT NULL,
      `weight` varchar(255) NOT NULL,
      `result` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_cost`
    ADD PRIMARY KEY (`id`)
  ";
  $queries[] = "
    ALTER TABLE `caldera_ongkir_cost`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT
  ";
  foreach ($queries as $query) $wpdb->query($query);
});

add_action('admin_menu', function () {

  add_submenu_page(
    'caldera-forms', 
    'RajaOngkir API key', 
    '<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-admin-network"></span>API Key</span>',
    'manage_options', 
    'caldera-ongkir-apikey', 
    function () {
      include CALDERA_ONGKIR_PATH . 'config-apikey.php';
    }
  );

  add_submenu_page(
    'caldera-forms', 
    'Caldera Ongkir', 
    '<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-store"></span>Origin Address</span>',
    'manage_options', 
    'caldera-ongkir-origin', 
    function () {
      include CALDERA_ONGKIR_PATH . 'config-origin.php';
    }
  );

});

add_action('rest_api_init', function () {

  register_rest_route( 'caldera-ongkir', '/address/', array(
      'methods'   => 'POST',
      'callback'  => 'calderaOngkirAddress',
    )
  ); 

});

function calderaOngkirAddress () {
  $term = $_POST['term'];
  global $wpdb;
  $result = new stdClass();
  $records = $wpdb->get_results("
    SELECT
      subdistrict_id id,
      CONCAT('Kecamatan ', subdistrict_name, ', ', type, ' ', city_name, ', ', province) text
    FROM caldera_ongkir_subdistrict
    LEFT JOIN caldera_ongkir_city ON caldera_ongkir_city.city_id = caldera_ongkir_subdistrict.city_id
    LEFT JOIN caldera_ongkir_province ON caldera_ongkir_province.province_id = caldera_ongkir_city.province_id
    WHERE CONCAT('Kecamatan ', subdistrict_name, ', ', type, ' ', city_name, ', ', province) LIKE '%{$term}%'
    LIMIT 10
  ");
  $result->results = $records;
  return $result;
}

function getCompleteAddressBySubdistrict ($subdistrict_id) {
  global $wpdb;
  return $wpdb->get_row("
    SELECT
      subdistrict_id id,
      CONCAT('Kecamatan ', subdistrict_name, ', ', type, ' ', city_name, ', ', province) text
    FROM caldera_ongkir_subdistrict
    LEFT JOIN caldera_ongkir_city ON caldera_ongkir_city.city_id = caldera_ongkir_subdistrict.city_id
    LEFT JOIN caldera_ongkir_province ON caldera_ongkir_province.province_id = caldera_ongkir_city.province_id
    WHERE subdistrict_id = {$subdistrict_id}
  ");
}