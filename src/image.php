<?php
require( dirname(__FILE__) . '/wp-load.php' );
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');



/**
 * function get id post
 * @return array id products
 */
function getIdPost(){
	global $wpdb;
	$prod_id = $wpdb->get_results('SELECT `id` FROM `wp_posts` WHERE `post_type` = "product"');
	$prod_id_array= array();
	foreach ($prod_id as $value){
		$prod_id_array[] = $value->id;
	}
	return $prod_id_array;
}


/**
 * @param  array
 * @return array product sku
 */
function getSKU($post_id){
	global $wpdb;
	$post_id_first = $post_id[0];
	$post_id_last  = $post_id[count($post_id)-1];
	$prod_sku = $wpdb->get_results("SELECT `post_id`, `meta_value` FROM `wp_postmeta` WHERE `meta_key` = '_sku' and `post_id` >= '$post_id_first' and `post_id` <= '$post_id_last'");
	return $prod_sku;
}


/**
 * @param  array products sku
 * @param  file name in folder
 * @return array nonexistent products
 */
function img($prod_sku, $get_file_name){
	$nonexistent = array();
	foreach ($prod_sku as $value_sku) {
		$id_prod = $value_sku->post_id;
		$sku = $value_sku->meta_value;
		// $done = false;
		foreach ($get_file_name as $name_file) {
			$name_file = str_replace('.jpeg', '', $name_file);
			if ($name_file == $sku) {
				$url_img = 'http://test11.peacedata.de/img/'.$sku.'.jpeg';
				Generate_Featured_Image($url_img, $id_prod);
				$done = true;
				break 1;
			}
		}
		// if (!$done) {
		// 	$nonexistent[$id_prod] = $sku; 
		// 	$json = file_get_contents('delete.json');
		// 	$json = json_decode($json, true);
		// 	$json[$id_prod] = $sku;
		// 	$json = json_encode($json);
		// 	file_put_contents('delete.json', $json);
		// }
	}
	return $nonexistent;
}


function Generate_Featured_Image( $image_url, $post_id  ){
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url);
	$filename = basename($image_url);
	if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
	else                                    $file = $upload_dir['basedir'] . '/' . $filename;
	file_put_contents($file, $image_data);

	$wp_filetype = wp_check_filetype($filename, null );
	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title' => sanitize_file_name($filename),
		'post_content' => '',
		'post_status' => 'inherit'
		);
	$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	$res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
	$res2 = set_post_thumbnail( $post_id, $attach_id );
}

$start = microtime(true);

$id_post = getIdPost();
$id_sku = getSKU($id_post);
unset($id_post);
$get_file_name = scandir(dirname(__FILE__) .'/img');
unset($get_file_name[0]);
unset($get_file_name[1]);
$nonexistent_array = img($id_sku, $get_file_name);
echo("<pre>");
echo("done<br>");
// print_r($nonexistent_array);

echo 'Load time : '.(microtime(true) - $start).' s.';

?>