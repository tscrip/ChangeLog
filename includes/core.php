<?php
require_once(PHP_INCLUDES.'DbHandler.php');
Class Core{
	function __construct(){

	}

	function GetRecent(){
		$db = new DbQuery();
	    $results = $db->GetRecentChanges();
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetChangers(){
		$db = new DbQuery();
	    $results = $db->GetChangers();
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetEnvironments(){
		$db = new DbQuery();
	    $results = $db->GetEnvironments();
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetSystems(){
		$db = new DbQuery();
	    $results = $db->GetSystems();
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetSystemNameArr(){
		$db = new DbQuery();
	    $results = $db->GetSystemNamesArr();
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return $results;
	}

	function GetDurations(){
		$db = new DbQuery();
	    $results = $db->GetDurations();
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetChangeByID($change_id){
		$db = new DbQuery();
	    $results = $db->GetChangeByID($change_id);
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetChangeBySystem($system){
		$db = new DbQuery();
	    $results = $db->GetChangeBySystem($system);
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function GetChangeBySearch($criteria){
		$db = new DbQuery();
	    $results = $db->GetChangeBySearch($criteria);
	    if (count($results)){
	    	$isobject = 1;
	    }
	    else{
	    	$isobject = 0;
	    }
	    return '{"success": "'.$isobject.'", "data": '.json_encode($results).'}';
	}

	function CreateChange($start,$duration,$summary,$change,$owner,$environment,$system){
		$db = new DbQuery();
	    $results = $db->CreateChange($start,$duration,$summary,$change,$owner,$environment,$system);
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": '.$isobject.', "data": '.json_encode($results).'}';

		return $results;
	}

	function GetCalendarChanges($from,$to){
		$db = new DbQuery();
	    $results = $db->GetCalendarChanges($from,$to);
	    if (count($results)){
	    	$isobject = "1";
	    }
	    else{
	    	$isobject = "0";
	    }
	    return '{"success": "'.$isobject.'", "result": '.json_encode($results).'}';
	}

	function CheckLogin($username,$password){
		if ($username != "" && $password != "" ){
	        // Open the connection 
	        $ldap_connection = ldap_connect("ldap://".LDAP_SERVER) or die ("Couldn't connect to AD!");
	        if ($ldap_connection){
        		ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION,3); 
	        	ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS,0); 
	        	$fqdn_username = DOMAIN_PREFIX."\\".$username;
	        	if (@ldap_bind($ldap_connection,str_replace('"','',$fqdn_username),str_replace('"','',$password))) {
	        		$_SESSION['Authenticated_User'] = $username;
	        		return array("success"=>"1","result"=>"Logged In");
	        	}
	            else
	            {
	                return array("success"=>"0","result"=>"Access Denied");
	            }
	    	}
	    	ldap_unbind($ldap_connection);
	    }
	    else
	    { 
	        return array("success"=>"0","result"=>"Access Denied");
		}
	}
	function LogOut(){
		$_SESSION = array();		
		session_destroy();
		return array("success"=>"1","result"=>"User Logged Out");
	}
}
?>