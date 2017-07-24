<?php
ini_set("memory_limit", "512M");
$date = date("dmoHis");
$file = dirname(__FILE__).'\product-'.$date.'.csv';

$first_row = array 
				(
					"0" => "wp_ID",
					"1" => "wp_post_date",
					"2" => "wp_post_modified",
					"3" => "wp_post_status",
					"4" => "wp_post_title",
					"5" => "wp_post_content",
					"6" => "wp_post_excerpt",
					"7" => "wp_post_parent",
					"8" => "wp_post_name",
					"9" => "wp_post_type",
					"10" => "wp_post_mime_type",
					"11" => "wp_ping_status",
					"12" => "wp_comment_status",
					"13" => "wp_menu_order",
					"14" => "wp_post_author",
					"15" => "tx_category",
					"16" => "tx_post_tag",
					"17" => "tx_post_format",
					"18" => "tx_product_cat",
					"19" => "tx_product_tag",
					"20" => "tx_product_shipping_class",
					"21" => "fi_thumbnail",
					"22" => "cf__wp_page_template",
					"23" => "cf__wp_attached_file",
					"24" => "cf__wp_attachment_context",
					"25" => "cf__wp_attachment_metadata",
					"26" => "cf__thumbnail_id",
					"27" => "cf__visibility",
					"28" => "cf__stock_status",
					"29" => "cf_total_sales",
					"30" => "cf__downloadable",
					"31" => "cf__virtual",
					"32" => "cf__product_image_gallery",
					"33" => "cf__regular_price",
					"34" => "cf__sale_price",
					"35" => "cf__tax_status",
					"36" => "cf__tax_class",
					"37" => "cf__purchase_note",
					"38" => "cf__featured",
					"39" => "cf__weight",
					"40" => "cf__length",
					"41" => "cf__width",
					"42" => "cf__height",
					"43" => "cf__sku",
					"44" => "cf__product_attributes",
					"45" => "cf__sale_price_dates_from",
					"46" => "cf__sale_price_dates_to",
					"47" => "cf__price",
					"48" => "cf__sold_individually",
					"49" => "cf__stock",
					"50" => "cf__backorders",
					"51" => "cf__manage_stock",
					"52" => "cf__upsell_ids",
					"53" => "cf__min_variation_price",
					"54" => "cf__max_variation_price",
					"55" => "cf__min_variation_regular_price",
					"56" => "cf__max_variation_regular_price",
					"57" => "cf__min_variation_sale_price",
					"58" => "cf__max_variation_sale_price",
					"59" => "cf__default_attributes",
					"60" => "cf__wp_old_slug",
					"61" => "cf__crosssell_ids",
					"62" => "cf__download_limit",
					"63" => "cf__download_expiry",
					"64" => "cf__file_paths",
					"65" => "cf_attribute_pa_color",
					"66" => "cf__wc_review_count",
					"67" => "cf__wc_rating_count",
					"68" => "cf__wc_average_rating",
					"69" => "cf__product_aliac"
				);




/* Чтение данных */
function openCSV($fileName){
	$csvData = file_get_contents($fileName);
	$lines = explode(PHP_EOL, $csvData);
	$array = array();
	foreach ($lines as $line) {
		$array[] = str_getcsv($line);
	}
	return ($array);
}

/* Запись данных */
function writeDataCSV($data, $filename){
	foreach ($data as $fields) {
		fputcsv($filename, $fields);
	}
	echo "--complete--";
}

/* 
	Создание key => value массива
	на основе старых данных
	с созданием валидной записи для категорий
*/
function create_cat($arrayCSV){
	$array = array();
	for ($i = 1; $i < count($arrayCSV); $i++){
		$temp_replace = str_replace("/ ", "+", $arrayCSV[$i][0]);
		$temp_explode = explode("/", $temp_replace);
		$temp_str = "";
		if (count($temp_explode) > 1) {
			$temp_str_first = "";
			foreach ($temp_explode as $value) {
				if ($temp_str == "") {
					$temp_str .= mb_strtolower($value, 'UTF-8').':'.$value.',';
					$temp_str_first = mb_strtolower($value, 'UTF-8');
				} else {
					$temp_str_raplace = str_replace(', ', '-', $value);
					$temp_str_raplace = str_replace('"', '', $temp_str_raplace);
					$temp_str_name = str_replace('"','', $value);
					$temp_str_name = str_replace(', ', ' | ', $temp_str_name);
					$temp_str .= $temp_str_first.'~'.mb_strtolower($temp_str_raplace, 'UTF-8').':'.$temp_str_name.',';
				}
			}
			$temp_str = substr($temp_str, 0, -1);
		} else {
			$temp_str = mb_strtolower($temp_explode[0], 'UTF-8').':'.$temp_explode[0];
			// print_r($arrayCSV[$i][1]);
		}
		$array[$arrayCSV[$i][1]] = $temp_str; 
	}
	// print_r($array);
	return $array;
}

function concatenation_array($first_array, $second_array, $description_array=NULL){
	echo "<pre>";
	$array = array();
	array_push($array, $description_array);
	$count_array = 0;
	$count_error = 0;
	// for ($i = 1; $i < count($first_array); $i++){
	for ($i = 1; $i < count($first_array); $i++){
		foreach ($second_array as $key => $value) {
			if ($first_array[$i][1] == $key) {
				// echo $first_array[$i][1].' - '.$key.' : '.$value.'<br>';
				$count_array+=1;
				$array[$i] = array();
				for ($j = 0; $j < count($description_array); $j++) {
					switch ($j) {
						case '1':
							$array[$i][$j] = '2017-06-07 11:35:00';
							break;
						case '2':
							$array[$i][$j] = '2017-06-07 11:35:00';
							break;
						case '3':
							$array[$i][$j] = 'publish';
							break;
						case '4':
							$array[$i][$j] = $first_array[$i][33];
							break;
						case '5':
							$array[$i][$j] = str_replace("<p>&nbsp;</p>", "", trim($first_array[$i][37]));
							break;
						case '6':
							$array[$i][$j] = trim($first_array[$i][36]);
							break;
						case '7':
							$array[$i][$j] = '0';
							break;
						case '8':
							$array[$i][$j] = $first_array[$i][35];
							break;
						case '9':
							$array[$i][$j] = 'product';
							break;
						case '11':
							$array[$i][$j] = 'closed';
							break;
						case '12':
							$array[$i][$j] = 'open';
							break;
						case '13':
							$array[$i][$j] = '0';
							break;
						case '14':
							$array[$i][$j] = 'name';
							break;
						case '18':
							$array[$i][$j] = $value;
							break;
						case '21':
							$array[$i][$j] = ''; // img_url
							break;
						case '33':
							$array[$i][$j] = str_replace(',', '.', $first_array[$i][6]);
							break;
						case '43':
							$array[$i][$j] = $first_array[$i][1];
							break;
						case '47':
							$array[$i][$j] = str_replace(',', '.', $first_array[$i][6]);
							break;
					 	default:
					 		$array[$i][$j] = '';
					 		break;
					 } 
				}
			} elseif ($first_array[$i][1] != $key){
				$count_error+=1;
			} 
		}
	}

	echo "Количество совпадений = $count_array<br>Количество несовпадений = $count_error<br>Количество товаров = ".count($first_array)."<br>";
	// print_r($array);
	return $array;
}
$start = microtime(true);
/* Создание нового файла */
if (!file_exists($file)) {
	$name = 'product-'.$date.'.csv';
	$file = fopen($name, 'w');	
} else die('Файл существует');

$firstCSV = openCSV('Kategorie-Produkt-Zuweisung.csv');
$secondCSV = openCSV('Produkte.csv');
// array_pop($firstCSV);

unset($firstCSV[0]);
unset($secondCSV[0]);
$array_cat = create_cat($firstCSV);
$array_done = concatenation_array($secondCSV, $array_cat, $first_row);
writeDataCSV($array_done, $file);


echo "<pre>";
echo 'Время выполнения : '.(microtime(true) - $start).' сек.';
// print_r($first_row);
// print_r($firstCSV);


?>