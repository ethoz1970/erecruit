

<?php


#Talener erecruit API intergration authored by Mario Moorhead Blackskymedia 2014 

##Settings
$serviceRoot = "http://erecruit.acme.com/restservices";
$entityID = "00000000-0000-0000-0000-000000000000";
$username = "API@acme.com";
$password = "FAKE";
##End Settings

$alldone = 0;
$positionID = 5000;
$c = authenticate($serviceRoot, $username, $password, $entityID);

$slipknot = 0;
$fakeme = $positionID;

while ( $positionID <= 99045) {

	$positionID ++;
	$results2 = getPosition($c, $serviceRoot, $positionID, $entityID);
	$testme = strlen($results2);

	if ( $testme == '204'){
		echo ' EMPTY POST ';
		$alldone++;
		echo $alldone;
		if ($alldone > 10)
		$positionID = 99999999;
	}

	else
	$alldone=0;

	/**
	 * here we have the result in a string it is not xml and needs to be parsed
	 **/
	$jobLisiting = array();
	$p = xml_parser_create();
	xml_parse_into_struct($p, $results2, $vals, $index);
	xml_parser_free($p);
	$record = 1;

	for ($i=0; $i < 80; $i++) { 
		
		if($vals[$i] == null)
		break;

		if($vals[$i]['tag'] == 'POSITIONTITLE'){
			$jobLisiting[$record]['position'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'FOLDERGROUP'){
			$jobLisiting[$record]['foldergroup'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'DATELASTOPENED'){
			$jobLisiting[$record]['date'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'FOLDERGROUP'){
			$jobLisiting[$record]['location'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'COMPANYNAME'){
			$jobLisiting[$record]['company'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'RECRUITERNAME'){
			$jobLisiting[$record]['recruiter'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'WEBDESCRIPTION'){
			$jobLisiting[$record]['body'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'POSITIONTYPE'){
			$jobLisiting[$record]['type'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'POSITIONID'){
			$jobLisiting[$record]['id'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'WEBPOSITIONTITLE'){
			$jobLisiting[$record]['web'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'ANNUALMAX'){
			$jobLisiting[$record]['annualmax'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'HOURLYMAX') {
			$jobLisiting[$record]['hourmax'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'YEARSEXPERIENCE') {
			$jobLisiting[$record]['yearsexperience'] = $vals[$i]['value'];
		}

		if($vals[$i]['tag'] == 'RECRUITERNAME') {
			$jobLisiting[$record]['recruitername'] = $vals[$i]['value'];
		}

	}


	// fix date format
	$re = "/([0-9]+\\/[0-9]+\\/[0-9]*+)/"; 
	$str = "7/12/2014 1:41:02 PM"; 
	preg_match($re, $jobLisiting[1]['date'], $matches);
	$jobLisiting[1]['date'] = $matches[0];
	$date = $matches[0];
	$dateArray = explode('/',$date);
	if ($dateArray['0'] < 10)
		$dateArray['0'] = '0'.$dateArray['0'];

	if ($dateArray['1'] < 10)
		$dateArray['1'] = '0'.$dateArray['1'];

	$date = $dateArray['2'].'-'.$dateArray['0'].'-'.$dateArray['1'];
	$date = $date.' 15:00:11';

	$postKey = date("y_m_d_h_i_s");

	$folder = $jobLisiting[1]['foldergroup'];
	$location = substr($jobLisiting[1]['foldergroup'], 0,2);

	$folderArray = explode('-',$folder);
	$slug = $jobLisiting[1]['position'].'-'.$postKey; 
	$slug = strtolower($slug);
	$slug = ltrim($slug);
	$slug = rtrim($slug);
	$slug = strtolower($slug);
	$slug = str_replace('/', '', $slug);
	$slug = str_replace('(', '', $slug);
	$slug = str_replace(')', '', $slug);
	$slug = str_replace('#', '', $slug);
	$slug = str_replace('.', '', $slug);
	$slug = str_replace(',', '', $slug);
	$slug = str_replace(' ', '-', $slug);


	$experience =  $jobLisiting[1]['yearsexperience'];
	$tempExperience = intval($experience);


	if ( strlen($experience) < 1)
		$tempExperience = 'nothing';

	if ($experience < 4 && $experience >0)
		$experience = 'JUNIOR';

	else if ($experience < 8 && $experience >3)
		$experience = 'MID';

	else if ($experience > 8 && $experience >7)
		$experience = 'SENIOR';

	$folder = $folderArray[1];

	switch ($location) {
		case 'NY':
			$location = 'NEWYORK';
			break;
		case 'LA':
			$location = 'LOSANGELES';
			break;
		case 'SF':
			$location = 'SANFRANCISCO';
			break;
		case 'Bo':
			$location = 'BOSTON';
			break;
		case 'CH':
			$location = 'CHICAGO';
			break;
		
		default:
			$location = 'REMOTE';
			break;
	}


	$position = $jobLisiting[1]['position'];
	$recruiter = $jobLisiting[1]['recruiter'];
	$rate = $jobLisiting[$record]['annualmax'];
	$company = $jobLisiting[1]['company'];
	$type = $jobLisiting[1]['type'];
	$web = $jobLisiting[1]['web'];

	$description = $jobLisiting[1]['body'];
	$description = str_replace("<![CDATA[", "", $description);
	$description = str_replace("]]>", "", $description);

	$foldergroup = $jobLisiting[1]['foldergroup'];
	$id = $jobLisiting[1]['id'];
	$tflag = 0;
	$result = null;
	$temp_id = null;
	
	if ($rate == '0.0000')
		$rate = null;
	
	$recruiter = $jobLisiting[$record]['recruitername'];
	// this switch statement has been reduced from original 70 items for portfolio purposes

	switch ($recruiter) {
		case 'Weston Eakman':
			$recruiter = 105;
			break;

		case 'Vincent Veltre':
			$recruiter = 73;
			break;

		case 'API User':
			$recruiter = 63;
			break;

		case 'Chris Sapka':
			$recruiter = 17;
			break;

		case 'Christian Free':
			$recruiter = 25;
			break;

		default:
			$recruiter = 19;
			break;
	}


	echo 'REC='.$recruiter;
	$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
	// Check connection
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$result = mysqli_query($con,"SELECT * FROM talenwp_tempbase where er_id = '$positionID'") ;
	
	while($row = mysqli_fetch_array($result)) {
		$temp_id = $row['id'];
	}

	if (isset($temp_id))
		$tflag = 12;

	if ( $web == '')
		$tflag = 5;

	if ( strlen($description) < 14)
		$tflag = 6;

	if ( $results2 == '1')
		$tflag = 9;

	if ($result == NULL)
		$tflag = 7;

	mysqli_close($con);

	echo 'POS='.$positionID.' ';

	if ($tflag < 5) {
	// check records status is it OPEN or CLOSED
		$url2 = "$serviceRoot/Position/$entityID/?StatusIDs=3&PositionID=$positionID";
		curl_setopt($c, CURLOPT_URL, $url2);
		curl_setopt($c, CURLOPT_HTTPGET, true);
		curl_setopt($c, CURLOPT_HEADER, false);
		$result2 = curl_exec($c);
		$postStatus = strlen($result2);
		echo ' STATUS='.$postStatus.' ';
		if ($postStatus > 205) {
			$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
			
			if (mysqli_connect_errno()) {
	  			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}


			mysqli_query($con,"INSERT INTO talenwp_posts (
			post_author, 
			post_date, 
			post_date_gmt, 
			post_content, 
			post_title,  
			post_excerpt,  
			post_status, 
			comment_status, 
			ping_status, 
			post_password, 
			post_name, 
			to_ping, 
			pinged, 
			post_modified, 
			post_modified_gmt, 
			post_content_filtered, 
			post_parent, 
			guid, 
			menu_order, 
			post_type
			) 

			VALUES ( 
			'55', 
			'$date' , 
			'$date' , 
			'$description', 
			'$web' ,  
			'$position' , 
			'publish' , 
			'closed' , 
			'closed' , 
			'' , 
			'$slug', 
			' ' , 
			' ', 
			'2014-03-07 15:00:23', 
			'2014-03-07 15:00:23', 
			'',
			'0', 
			'$slug', 
			'0', 
			'jobs' 
			)");

			mysqli_close($con);


			$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
			if (mysqli_connect_errno()) {
	  			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$result = mysqli_query($con,"SELECT * FROM talenwp_posts ORDER BY ID DESC LIMIT 1");
			while($row = mysqli_fetch_array($result)) {
	  			$temp_id = $row['ID'];
			}

			mysqli_close($con);


			// add post meta_data for custom fields
			$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
			if (mysqli_connect_errno()) {
	  			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'experience_level', 
			'$experience' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'location', 
			'$location' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'_location', 
			'field_1' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'compensation', 
			'$rate' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'_compensation', 
			'field_52ce7c4af4e6a' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'position', 
			'$folder' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'_position', 
			'field_2' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'company', 
			'$company' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'_company', 
			'field_5' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'contact', 
			'21' 
			)");

			mysqli_query($con,"INSERT INTO talenwp_postmeta (
			post_id, 
			meta_key,
			meta_value
			) 

			VALUES ( 
			'$temp_id', 
			'_contact', 
			'field_52dbed02b51ec' 
			)");

			mysqli_close($con);

			// CREATE post in TEMP database
			$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
			if (mysqli_connect_errno()) {
	  			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			echo ' WRITTING TO TEMP ';

			mysqli_query($con,"INSERT INTO talenwp_tempbase (
			wp_id, 
			title, 
			position, 
			recruiter, 
			company, 
			location,
			pdate,
			er_id
			) 

			VALUES ( 
			'$temp_id', 
			'$folder',
			'$position' , 
			'$recruiter' , 
			'$company',
			'$location', 
			'$date',
			'$id'
			)");

			$result = mysqli_query($con,"SELECT * FROM talenwp_tempbase");

			mysqli_close($con);

		}


	}

	else {
		// here we catch records that already exist in the TEMP database
		if ($tflag == 12) {
			// this is a real record but it is either closed or already exist and needs to be updated
			$url2 = "$serviceRoot/Position/$entityID/?StatusIDs=3&PositionID=$positionID";

			curl_setopt($c, CURLOPT_URL, $url2);
			curl_setopt($c, CURLOPT_HTTPGET, true);
			curl_setopt($c, CURLOPT_HEADER, false);
			
			$result2 = curl_exec($c);
			$postStatus = strlen($result2);
			echo ' STATUS='.$postStatus.' ';
			// if record already exist and is open UPDATE

			if ($tflag = 12 && $postStatus > 204){
				echo ' UPDATE ';
				$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
				
				if (mysqli_connect_errno()) {
		  			echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

				$resulter = mysqli_query($con,"SELECT * FROM talenwp_tempbase where er_id = '$positionID'");

				while($row = mysqli_fetch_array($resulter)) {
			  		$wp_poster = $row['wp_id'];
				}

				mysqli_close($con);

				if (isset($wp_poster)){
					// Open up database connection and create WP post
					$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
				
					if (mysqli_connect_errno()) {
			  			echo "Failed to connect to MySQL: " . mysqli_connect_error();
					}

					echo 'Recruiter='.$recruiter.' ';

					mysqli_query($con,"UPDATE talenwp_posts 
					SET  
					post_author='99', 
					post_date='$date', 
					post_date_gmt='$date', 
					post_content='$description', 
					post_title='$web',  
					post_excerpt='$position',  
					post_status='publish', 
					comment_status='closed', 
					ping_status='closed', 
					post_password='', 
					to_ping=' ', 
					pinged=' ', 
					post_modified='2014-03-07 15:00:23', 
					post_modified_gmt='2014-03-07 15:00:23', 
					post_content_filtered='', 
					post_parent='0', 
					guid='$slug', 
					menu_order='0', 
					post_type='jobs'
								
					WHERE ID='$wp_poster'");

					mysqli_close($con);

					// add post meta_data for custom fields
					$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
					if (mysqli_connect_errno()) {
				  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
					}

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$experience'
					WHERE post_id='$wp_poster' AND meta_key='experience_level' ");

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$location'
					WHERE post_id='$wp_poster' AND meta_key='location' ");

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$rate'
					WHERE post_id='$wp_poster' AND meta_key='compensation' ");

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$folder'
					WHERE post_id='$wp_poster' AND meta_key='position' ");

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$company'
					WHERE post_id='$wp_poster' AND meta_key='company' ");

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$rate'
					WHERE post_id='$wp_poster' AND meta_key='compensation' ");

					mysqli_query($con,"UPDATE talenwp_postmeta 
					SET
					meta_value='$recruiter'
					WHERE post_id='$wp_poster' AND meta_key='contact' ");

					mysqli_close($con);
				}
			}




			// if record is closed DELETE it
			if ($postStatus < 205) {
				echo ' CLOSED RECORD ';

				$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
				
				if (mysqli_connect_errno()) {
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

				$resulter = mysqli_query($con,"SELECT * FROM talenwp_tempbase where er_id = '$positionID'");

				while($row = mysqli_fetch_array($resulter)) {
			  		$wp_poster = $row['wp_id'];
				}

				mysqli_close($con);

				if (isset($wp_poster)){
					$con=mysqli_connect("localhost","ethoz","drupal2010","ethoz_talener3_mpwp");
					mysqli_query($con,"delete from talenwp_tempbase where er_id = '$positionID'" );
					mysqli_query($con,"delete from talenwp_posts where id = '$wp_poster'" );
					mysqli_query($con,"delete from talenwp_postmeta where post_id = '$wp_poster'" );
					mysqli_close($con);
					echo ' DELETE WP ';
				}

				else
				echo ' ALREADY GONE ';
			}

		}

	}

}

## Returns an authorized curl resource that can be used with subsequent requests
function authenticate($serviceRoot, $username, $password, $entityID) {
	$authenticate = $serviceRoot . "/Authenticate";
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, $authenticate);
	curl_setopt($c, CURLOPT_POST, true);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_HEADER, true);
	curl_setopt($c, CURLINFO_HEADER_OUT, true);
	curl_setopt($c, CURLOPT_COOKIEFILE, 'cookie.txt');
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($c, CURLOPT_VERBOSE, true);
	curl_setopt($c, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt($c, CURLOPT_NOPROGRESS, true);
	$data = array('Username' => $username, 'Password' => $password, 'EntityID' => $entityID);
	curl_setopt($c, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($c);
	return $c;
}


function getPosition($c, $serviceRoot, $positionID, $entityID) {
	$url = "$serviceRoot/Position/$entityID/?PositionID=$positionID";
	curl_setopt($c, CURLOPT_URL, $url);
	curl_setopt($c, CURLOPT_HTTPGET, true);
	curl_setopt($c, CURLOPT_HEADER, false);
	$result = curl_exec($c);
	return $result;
}



?>