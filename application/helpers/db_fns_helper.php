<?php

// connect to the mlm database
function db_connect()
{
   $result = new mysqli('localhost', 'smstestadmin', '5m5T3st', 'mlm'); 
   if (!$result)
      return false;
   return $result;
}

function db_connect2()
{
	$servername = 'bbtriwilhst01.trimart.com,1433';
	$username = 'Test';	 //'TRIMART\SMSTestAdmin';
	$password = 'test';
	$database = 'STORESQL';
	
	$connectionInfo = array( "Database"=>$database);//, "UID"=>$username, "PWD"=>$password
	$result = sqlsrv_connect( $serverName, $connectionInfo);
	
	if( $result ) {
     echo "Connection established.<br />";
	}
	else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

}



?>
