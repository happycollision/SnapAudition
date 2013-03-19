<?php
function in_url($search_string){
    if(preg_match('#('.$search_string.')#i',$_SERVER['REQUEST_URI'])) return true;
    return false;
}

function format_date_string($format, $date_in){
	if(!is_numeric($date_in)){ $date_in_temp = strtotime($date_in);
	echo "\$date_in = $date_in and \$date_in_temp is $date_in_temp<br>";}
	if(isset($date_in_temp)){ if($date_in_temp!=false) $date_in = $date_in_temp;}
	return date($format,$date_in);
}

function strip_zeros_from_date( $marked_string="" ) {
  // first remove the marked zeros
  $no_zeros = str_replace('*0', '', $marked_string);
  // then remove any remaining marks
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

## backpost
/*
    This function performs three functions.  If first param is empty, it will pull data from $_SESSION['POST']
    which may have been set right before a page redirect with form data.  It will then capture that data in a
    new static variable ($old_post_info) for future calls. Returns $old_post_info.
    
    If param 1 has a value, it tests to see if $_SESSION['POST'] once had that array key and returns a boolean.
    
    If params 1 and 2 are set, it either echos the value once held in $_SESSION['POST'][$array_key], or it
    returns the value, based on whether $echo evaluates true or false.
*/
function backpost($array_key="",$echo=""){
    static $old_post_info;
    if($echo==="") unset($echo);
    if(empty($array_key)){
        if(isset($_SESSION['POST'])) {
            $old_post_info = $_SESSION['POST'];
            unset($_SESSION['POST']);
            //ddprint($site_info);
        }
        return $old_post_info;
    }
    if(isset($old_post_info[$array_key])){
        if(isset($echo) && $echo == true){
            echo $old_post_info[$array_key];
            return;
        }elseif(isset($echo) && $echo == false){
            return $old_post_info[$array_key];
        }
        return true;
    }
    return false;
}

function messages($msg="",$type="") { //will store or display any un-displayed messages
	global $session;
	$messages_output = "\n";
	
	if (!empty($msg)) {
		$session->non_session_message($msg, $type);
	} else {
		$messages = $session->get_messages();
		foreach($messages as $message){
			$messages_output .= "<p class=\"message {$message['type']}\">{$message['message']}</p>\n";
		}
		echo $messages_output;
	}
}

## Required Fields Spinner
/*
    Will take the values inside $_POST and distribute them to the object 
    that is passed in.  Will also check empty responses from the form
    used and will redirect with messaging if the field was required.
*/
function required_fields_spinner($object, $required_fields=''){
    global $session, $form_errors;
    foreach($_POST as $attribute => $value){
        if(empty($value) && is_array($required_fields)) {
            //check to see if the field was required
            $user_error = array_key_exists($attribute,$required_fields) ? true : false;
            if($user_error){
            	$vars = array(
            		'error_message' => "The {$required_fields[$attribute]} field is required.",
            		'object_id' => $object->id,
            		'field_name' => $required_fields[$attribute],
            		'field_id' => $attribute
            	);
            	            	
                $this_error = new FormError($vars);
                $form_errors[] = $this_error; // array($form_field, $message_to_user)
            }
        }
        if($object->has_attribute($attribute)){
            $object->$attribute = $value;
        }
    }
}
##  Fields Spinner
/*
    Similar to the function above, but can be used for multi object saves.
    It also will not thro up any errors in $form_errors. Will merely match
    data in an array (which must be passed in for this function) to 
    corresponding properties of an passed object.
*/
function fields_spinner($object, $attribute_values_array){
    foreach($attribute_values_array as $attribute => $value){
        if($object->has_attribute($attribute)){
            $object->$attribute = $value;
        }
    }
}

function __autoload($class_name) {
	$class_name = preg_replace( '/(.)([A-Z])/', '$1_$2', $class_name);
	$class_name = strtolower($class_name);
	$path = LIB_PATH.DS."{$class_name}.php";
	if(file_exists($path)) {
		require_once($path);
	} else {
		die("The file {$class_name}.php could not be found.");
	}
}

function template_part($template="") { //temp workaround for variable scope is defining the template path and including directly, not in this function.
	include(SITE_ROOT.DS.'public'.DS.'templates'.DS.$template.'.php');
}

function log_action($action, $message="") {
    global $session;
    $logfiledir = LIB_PATH.DS.'logs'.DS.date('Y').DS.date('m');
    is_dir($logfiledir) ? NULL : mkdir($logfiledir,0755,true);
    $logfile = $logfiledir . DS . 'log.txt';
    $new = file_exists($logfile) ? false : true;
    if($handle = fopen($logfile, 'a')) { // 'a' means append
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action}: {$message}\n";
        fwrite($handle, $content);
        fclose($handle);
        if($new) { chmod($logfile, 0755); }
    } else {
        $session->message('Could not open log file for writing.  This may not have affected performance, but please alert the application developer.','error');
    }
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

// Time format is UNIX timestamp or
// PHP strtotime compatible strings
function dateDiff($time1, $time2, $precision = 6, $max_interval = 'year') {
	// If not numeric then convert texts to unix timestamps
	if (!is_numeric($time1)) $time1_temp = strtotime($time1);
	if (!is_numeric($time2)) $time2_temp = strtotime($time2);
	
	//if a timestamp is converted to a timestamp, it will fail.  This ensures that all mis-read timestamps will remain intact.
	if(isset($time1_temp)){ if($time1_temp!=false) $time1 = $time1_temp;}
	if(isset($time2_temp)){ if($time2_temp!=false) $time2 = $time2_temp;}
	
	// If time1 is bigger than time2
	// Then swap time1 and time2
	if ($time1 > $time2) {
		$ttime = $time1;
		$time1 = $time2;
		$time2 = $ttime;
	}
	
	// Set up intervals and diffs arrays
	$intervals = array('year','month','day','hour','minute','second');
	
	//fix possible user input error
	$max_interval = rtrim(trim(strtolower($max_interval)),'s');
	
	//downsize array to the maximum interval
	if(in_array($max_interval,$intervals)){
		while(isset($max_interval)&&$max_interval!=reset($intervals)) {
			array_shift($intervals);
		}
	}
	$diffs = array();
	
	// Loop thru all intervals
	foreach ($intervals as $interval) {
		// Set default diff to 0
		$diffs[$interval] = 0;
		// Create temp time from time1 and interval
		$ttime = strtotime("+1 " . $interval, $time1);
		// Loop until temp time is smaller than time2
		while ($time2 >= $ttime) {
			$time1 = $ttime;
			$diffs[$interval]++;
			// Create new temp time from time1 and interval
			$ttime = strtotime("+1 " . $interval, $time1);
		}
	}
	
	$count = 0;
	$times = array();
	// Loop thru all diffs
	foreach ($diffs as $interval => $value) {
		// Break if we have needed precission
		if ($count >= $precision) {
			break;
		}
		// Add value and interval 
		// if value is bigger than 0
		if ($value > 0) {
			// Add s if value is not 1
			if ($value != 1) {
				$interval .= "s";
			}
			// Add value and interval to times array
			$times[] = $value . " " . $interval;
			$count++;
		}
	}
	
	// Return string with times
	$output = implode(", ", $times);
	return !empty($output) ? $output : '0 seconds';
}

//testing functions

function ddprint($var){
	echo '<p><pre>';
	print_r($var);
	echo '</pre></p>';
}
?>