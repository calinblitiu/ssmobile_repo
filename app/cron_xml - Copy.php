<?php

// Chage this parameter to exist Dev mode.
define( 'IS_DEV', false );

if( IS_DEV ) {
	define( 'URL_AUSTRIAN_DECORATIVE', 'http://192.168.0.2/lhs_workspace/xml/group-feeds/Austrian-Decorative-Art.xml' );
	define( 'URL_AUSTRIAN_FINE', 'http://192.168.0.2/lhs_workspace/xml/group-feeds/Austrian-Fine-Art.xml' );
	define( 'URL_GERMAN_DECORATIVE', 'http://192.168.0.2/lhs_workspace/xml/group-feeds/German-Decorative-Art.xml' );
	define( 'URL_GERMAN_FINE', 'http://192.168.0.2/lhs_workspace/xml/group-feeds/German-Fine-Art.xml' );
	define( 'URL_SINGLE_OBJECT', 'http://192.168.0.2/lhs_workspace/xml/object-feeds/single-object-2.xml' );
	define( 'URL_ARTIST', 'http://192.168.0.2/lhs_workspace/xml/artist-feed/artist-works-list.xml' );

	$db_url = 'localhost';
	$db_user = 'root';
	$db_password = '';
	$db_name = 'neue-galerie';
} else {
	// define( 'URL_AUSTRIAN_DECORATIVE', 'http://198.254.126.68:8181/mwebcgi/mwebxml.exe?request=advanced;_t1114=Austrian;_t1107=Decorative%20Art;caller=9595443823614278044448545611210834866145' );
	// define( 'URL_AUSTRIAN_FINE', 'http://198.254.126.68:8181/mwebcgi/mwebxml.exe?request=advanced;_t1114=Austrian;_t1107=Fine%20Art;caller=9595443823614278044448545611210834866145' );
	// define( 'URL_GERMAN_DECORATIVE', 'http://198.254.126.68:8181/mwebcgi/mwebxml.exe?request=advanced;_t1114=German;_t1107=Decorative%20Art;caller=9595443823614278044448545611210834866145' );
	// define( 'URL_GERMAN_FINE', 'http://198.254.126.68:8181/mwebcgi/mwebxml.exe?request=advanced;_t1114=German;_t1107=Fine%20Art;caller=9595443823614278044448545611210834866145' );
	// define( 'URL_SINGLE_OBJECT', 'http://198.254.126.68:8181/mwebcgi/mwebxml.exe?request=record;key=[KEY];caller=9595443823614278044448545611210834866145' );
	// define( 'URL_ARTIST', 'http://198.254.126.68:8181/mwebcgi/mwebxml.exe?request=advanced;_t1102=Gustav%20Klimt;caller=9595443823614278044448545611210834866145' );

	// $db_url = 'localhost';
	// $db_user = 'root';
	// $db_password = 'asdzxcasdzxcasdzxc';
	// $db_name = 'neue';

	define( 'URL_AUSTRIAN_DECORATIVE', './xml/austrian_decorative.xml' );
	define( 'URL_AUSTRIAN_FINE', './xml/austrian_arts.xml' );
	define( 'URL_GERMAN_DECORATIVE', './xml/german_decorative.xml' );
	define( 'URL_GERMAN_FINE', './xml/german_arts.xml' );
	define( 'URL_SINGLE_OBJECT', './xml/single.xml' );
	define( 'URL_ARTIST', './xml/artist.xml' );

	$db_url = 'dbserver.dev.38526f25-2265-487e-a66e-e97ab91e859d.drush.in';
	$db_user = 'pantheon';
	$db_password = 'afa3e3a521f24a0d8b1162390d47a252';
	$db_name = 'pantheon';
	$db_port = '27065';
}

// Image Path
define( 'IMAGE_SAVE_PATH', 'sites/default/files/' );

// set timezone
date_default_timezone_set("UTC");

// Define of url
$arrURLGroup = array(
	array( 
		'url' => URL_AUSTRIAN_DECORATIVE,
		'culture' => 'Austrian',
		'category' =>'Decorative Arts'
	),
	array( 
		'url' => URL_AUSTRIAN_FINE,
		'culture' => 'Austrian',
		'category' =>'Fine Arts'
	),
	array( 
		'url' => URL_GERMAN_DECORATIVE,
		'culture' => 'German',
		'category' =>'Decorative Arts'
	),
	array( 
		'url' => URL_GERMAN_FINE,
		'culture' => 'German',
		'category' =>'Fine Arts'
	),
);

// DB Connection
$db_conn = mysql_connect($db_url.":".$db_port, $db_user, $db_password);
mysql_select_db($db_name, $db_conn);


function funcInsertDB( $tableName, $arrField ) {
	global $db_conn;

	$sql = 'INSERT INTO ' . $tableName . ' SET ';

	$flag = true;
	foreach ($arrField as $key => $val) {
		if (!$flag) $sql .= ', ';
		$flag = false;

		$sql .= $key . ' = \'' . str_replace('\'', '\\\'', $val) . '\'';
	}

	mysql_query( $sql ) or die( mysql_error($db_conn) . ':<br>' . $sql . '<br><br>' );

	return mysql_insert_id();
}

function funcDownloadImage($src, $dest) {

	// Create Folder
	$arrDestFolder = explode('/', $dest);
	$strDest = '';
	for ($i = 0; $i < count($arrDestFolder) - 1; $i++) {
		$strDest .= $arrDestFolder[$i] . '/';
		if( !file_exists( $strDest ) ) mkdir( $strDest );
	}

	// Remove existing file
	if (file_exists($dest)) unlink($dest);

	// Dowonload Image
	$ch = curl_init($src);
	$fp = fopen($dest, 'w+');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}

// **************** Artist List ***************** //

$artist_xml=simplexml_load_file(URL_ARTIST) or die("Error: Cannot create object");

foreach ($artist_xml->subset[0] as $record){1

	// Check Exist
	if(!isset($record->Maker)) continue;

	// Parameter
	$name = $record->Maker; // "Maker";

	$arrName = explode(' ', $name);

	$first_name = $arrName[0];
	$last_name = $arrName[1];
	$image_url = $record->image->fullimage; // image.fullimage;
	$description = "";
	$birth_year = "";
	$death_year = "";
	$birth_place = "";
	$death_place = "";

	// Check Duplicate
	$sql = 'SELECT nid FROM node WHERE type = \'artist\' AND title = \'' . $name . '\'';
	$query = mysql_query( $sql );

	if( mysql_num_rows($query) > 0 ) continue;

	// Step 1. Insert into table "node"

	$arrField = array(
		'type' => 'artist',
		'title' => $name,
		'vid' => time(),
		'uid' => '0',
		'created' => time(),
		'changed' => time()
	);
	$nid = funcInsertDB( "node", $arrField );

	// Step 3. Insert into table "node_revisions"
	$arrField = array(
		'nid' => $nid,
		'uid' => '0',
		'title' => $name,
		'body' => $description,
		'teaser' => $description,
		'timestamp' => time(),
		'format' => '1',
		'log' => ''
	);
	$vid = funcInsertDB( "node_revisions", $arrField );

	// Step 2. Update talbe "node"
	$sql = 'UPDATE node set vid = ' . $vid . ' WHERE nid = ' . $nid;
	mysql_query( $sql );

	// Step 4. Insert into table "content_type_artist"
	$arrField = array(
		'nid' => $nid,
		'vid' => $vid,
		'field_mimsey_id_value' => '0',
		'field_preferred_name_value' => $name,
		'field_firstmid_name_value' => $first_name,
		'field_lastsuff_name_value' => $last_name,
		'field_birth_date_value' => $birth_year . 'T00:00:00',
		'field_death_date_value' => $death_year . 'T00:00:00',
		'field_birth_place_value' => $birth_place,
		'field_death_place_value' => $death_place,
		'field_nationality_value' => '',
		'field_occupation_value' => '',
		'field_bio_value' => '',
	);
	funcInsertDB( "content_type_artist", $arrField );

	// Step 5. Download the image ( $image_url ) and save it into /sites/default/files/[$new_filename].jpg
	$new_filename = strtolower(str_replace(' ', '', $name));
	$file_ext = substr($image_url, strlen($image_url)-3);

	funcDownloadImage( $image_url, IMAGE_SAVE_PATH . $new_filename.'.'.$file_ext );
	funcDownloadImage( $image_url, IMAGE_SAVE_PATH . 'imagecache/artist_profile_large/' . $new_filename.'.'.$file_ext );

	// Step 6 Insert into table "files"

	$arrField = array(
		'uid' => '0',
		'filename' => $new_filename.'.'.$file_ext,
		'filepath' => IMAGE_SAVE_PATH.$new_filename.'.'.$file_ext,
		'filemime' => 'image/' . ( $file_ext == 'jpg' ? 'jpeg' : $file_ext ),
		'filesize' => 100,
		'status' => '1',
		'timestamp' => time()
	);

	$fid = funcInsertDB( "files", $arrField );

	// Step 6. Insert into table "content_field_page_main_image"
	$arrField = array(
		'nid' => $nid,
		'vid' => $vid,
		'field_page_main_image_fid' => $fid,
		'field_page_main_image_list' => '1',
		'field_page_main_image_data' => 'a:2:{s:3:"alt";s:0:"";s:5:"title";s:0:"";}'
	);
	
	funcInsertDB( "content_field_page_main_image", $arrField );
} // end of foreach: "Artist List"

// **************** Group Feeds ***************** //
foreach( $arrURLGroup as $urlItem ) {
	$group_xml=simplexml_load_file($urlItem['url']) or die("Error: Cannot create object");
	foreach ($group_xml->subset[0] as $group_record){
		// get key
		$object_key = $group_record['key'];

		// get single object xml by ($austrian_decor_key)
		$single_xml=simplexml_load_file( str_replace('[KEY]', $object_key, URL_SINGLE_OBJECT) ) or die("Error: Cannot create object");

		// Parameter
		$title = $single_xml->Title; // title
		$maker = $single_xml->Maker[1]; // Maker
		$place_made = '';
		$date_made = $single_xml->Date_Made; // Date_Made
		$materials = $single_xml->Materials; // Materials
		$measurements = '';
		$credit_line = $single_xml->Credit_Line; // Credit_Line
		$location = $single_xml->Location; // Location
		$earlist_year = $single_xml->ID_Number; // ID_Number
		$latest_year = $single_xml->ID_Number; // ID_Number
		$object_category = $urlItem['category']; // Fixed : "Decorative Arts" or "Fine Arts"
		$object_culture = $urlItem['culture']; // Fixed : "Austrian" or "German"
		$on_view_status = $single_xml->Location; // Location
		$provenance = ''; // Provenance ( sum of items )

		foreach($single_xml->Provenance as $p){
			$provenance .= $p."\n\n";
		}

		$image_url_1 = $single_xml->images->image->thumbnail; // image.thumbnail
		$image_url_2 = $single_xml->images->image->fullimage; // image.fullimage

		// Check Duplicate
		$sql = 'SELECT nid FROM node WHERE type = \'art_object\' AND title = \'' . $title . '\'';
		$query = mysql_query( $sql );
		if( mysql_num_rows($query) > 0 ) continue;

		// Step 1. Insert into table "node"

		$arrField = array(
			'type' => 'art_object',
			'title' => $title,
			'vid' => time(),
			'uid' => '0',
			'created' => time(),
			'changed' => time()
		);

		$nid = funcInsertDB( "node", $arrField );

		// Step 3. Insert into table "node_revisions"
		$arrField = array(
			'nid' => $nid,
			'uid' => '0',
			'title' => $name,
			'body' => '',
			'teaser' => '',
			'timestamp' => time(),
			'format' => '1',
			'log' => ''
		);
		$vid = funcInsertDB( "node_revisions", $arrField );

		// Step 2. Update talbe "node"
		$sql = 'UPDATE node set vid = ' . $vid . ' WHERE nid = ' . $nid;
		mysql_query( $sql );

		// Step 4. Get the maker_nid from "node"
		$sql = 'SELECT nid FROM node WHERE title = \'' . $maker . '\' AND type = "artist"';
		$query = mysql_query( $sql );
		$maker_nid = mysql_num_rows($query) > 0 ? mysql_result($query, 0, 0) : 0;

		// Step 5. Download the image ( $image_url_1 ) and save it into 
		// sites/default/files/thumb_rollovers/NG_Collection/Larger_Images/' . date('Y') . '/' . $randomvalue . '.jpg'
		// sites/default/files/imagecache/slideshow_large_image/thumb_rollovers/NG_Collection/Larger_Images/' . date('Y') . '/' . $randomvalue . '.jpg'
			
		$file_ext = substr($image_url_1, strlen($image_url_1)-3);
		$image_thumb = time() . '.' . $file_ext;

		funcDownloadImage( $image_url_1, IMAGE_SAVE_PATH . 'imagecache/exhibition_large/'.$image_thumb);
		funcDownloadImage( $image_url_1, IMAGE_SAVE_PATH . 'thumb_rollovers/NG_Collection/Larger_Images/' . date('Y') . '/' . $image_thumb );
		funcDownloadImage( $image_url_1, IMAGE_SAVE_PATH . 'resized/NG_Collection/Larger_Images/' . date('Y') . '/' . $image_thumb );

		// Step 6 Insert into table "files"
		$arrField = array(
			'uid' => '0',
			'filename' => $image_thumb,
			'filepath' => IMAGE_SAVE_PATH.'thumb_rollovers/NG_Collection/Larger_Images/' . date('Y') . '/' . $image_thumb,
			'filemime' => 'image/' . ( $file_ext == 'jpg' ? 'jpeg' : $file_ext ),
			'filesize' => 100,
			'status' => '1',
			'timestamp' => time()
		);
		$fid_1 = funcInsertDB( "files", $arrField );

		// Step 7. Download the image ( $image_url_1 ) and save it into 'sites/default/files/resized/NG_Collection/Larger_Images/' . date('Y') . '/' . $randomvalue . '.jpg'
		// Step 8 Insert into table "files"
		$arrField = array(
			'uid' => '0',
			'filename' => $image_thumb,
			'filepath' => IMAGE_SAVE_PATH.'resized/NG_Collection/Larger_Images/' . date('Y') . '/' . $image_thumb,
			'filemime' => 'image/' . ( $file_ext == 'jpg' ? 'jpeg' : $file_ext ),
			'filesize' => 100,
			'status' => '1',
			'timestamp' => time()
		);
		$fid_2 = funcInsertDB( "files", $arrField );

		// Step 9. Download the image ( $image_url_2 ) and save it into
		// sites/default/files/$vandomvalue1.png'
		// sites/default/files/imagecache/exhibition_large/$vandomvalue1.png
		$file_ext = substr($image_url_2, strlen($image_url_2)-3);
		$image_full = time() . '.' . $file_ext;

		funcDownloadImage( $image_url_2, IMAGE_SAVE_PATH . $image_full);
		funcDownloadImage( $image_url_2, IMAGE_SAVE_PATH .'imagecache/slideshow_large_image/thumb_rollovers/NG_Collection/Larger_Images/' . date('Y') . '/' . $image_full );

		// Step 10 Insert into table "files"
		$arrField = array(
			'uid' => '0',
			'filename' => $image_full,
			'filepath' => IMAGE_SAVE_PATH . $image_full,
			'filemime' => 'image/' . ( $file_ext == 'jpg' ? 'jpeg' : $file_ext ),
			'filesize' => 100,
			'status' => '1',
			'timestamp' => time()
		);
		$fid_3 = funcInsertDB( "files", $arrField );

		// Step 11. Insert into table "content_field_thumbnail"
		$arrField = array(
			'nid' => $nid,
			'vid' => $vid,
			'field_thumbnail_fid' => $fid_3,
			'field_thumbnail_list' => '1',
			'field_thumbnail_data' => 'a:2:{s:3:"alt";s:0:"";s:5:"title";s:0:"";}'
		);
		funcInsertDB( "content_field_thumbnail", $arrField );

		// Step 12. Insert into table "content_type_art_object"
		$arrField = array(
			'nid' => $nid,
			'vid' => $vid,
			'field_art_object_mimsey_id_value' => '0',
			'field_name_title_value' => $name,
			'field_maker_value' => $maker,
			'field_place_made_value' => $place_made,
			'field_date_made_value' => $date_made,
			'field_materials_value' => $materials,
			'field_measurements_value' => $measurements,
			'field_credit_line_value' => $credit_line,
			'field_location_value' => $location,
			'field_earliest_year_value' => $earlist_year,
			'field_latest_year_value' => $latest_year,
			'field_art_object_category_value' => $object_category,
			'field_art_object_culture_value' => $object_culture,
			'field_media_path_value' => IMAGE_SAVE_PATH.'resized/NG_Collection/Larger_Images/' . date('Y') . '/' . $image_thumb,
			'field_artist_reference_nid' => $maker_nid,
			'field_thumbnail_rollover_fid' => $fid_1,
			'field_thumbnail_rollover_list' => '1',
			'field_thumbnail_rollover_data' => 'a:2:{s:3:"alt";s:0:"";s:5:"title";s:0:"";}',
			'field_art_object_german_title_value' => '',
			'field_credit_line_format' => '2',
			'field_art_object_on_view_status_value' => $on_view_status,
			'field_provenance_information_value' => $provenance,
			'field_provenance_information_format' => '2'
		);
		funcInsertDB( "content_type_art_object", $arrField );
	}// end of group-feed xml
}

?>