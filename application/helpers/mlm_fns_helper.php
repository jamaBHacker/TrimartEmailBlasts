<?php

// do we already have a record of this subscriber?
function subscriber_exists($email)
{
  /*if(!$email){
	  echo "<p>User not set</p>";
    return false;
  }*/

  if(!($conn=db_connect1())){
	  echo "<p>Could not connect to database</p>";
    return false;
  }

  $query = "select count(*) from clt_tab where F1573 = '$email'";

  $result = $conn->query($query);
  if(!$result){
	  echo "<p>No results to show</p>";
    return false;
  }
  $row = $result->fetch_array();
  return ($row[0]>0);
}

// is this user address subscribed to this list?
function subscribed($user, $listid)
{
  if(!$user||!$listid)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "select count(*) from sub_lists where email = '$user'
            and listid = $listid";

  $result = $conn->query($query);
  if(!$result)
    return false;
  $row=$result->fetch_array();
  return ($row[0]>0);
}

// is this listid the id of a list?
function list_exists($listid)
{
  if(!$listid)
  {
	 echo "<p>List id not set</p>";
    return false;
  }

  if(!($conn=db_connect()))
  {
	 echo "<p>Could not connect to database</p>";
    return false;
  }

  $query = "select count(*) from lists where listid = '$listid'";

  $result = $conn->query($query);
  if(!$result)
  {
	  echo "<p>No results to show</p>";
    return false;
  }
  $row=$result->fetch_array();
  return ($row[0]>0);
}

//get the user addresses of customers in database
function address_book()
{
	$CI = & get_instance();
	
	$list = array();
	
	$result = $CI->trimart->get_addresses();
	
	$i = 0;
	foreach($result as $row)
	{
        $list[$i] = array($row['F1155'],$row['F1573']);
		$i++;
	}
	
  
  return $list;
}


// get the name of the person with this user
function get_real_name($user)
{
  if(!$user)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "select F1155 from STORESQL.dbo.CLK_TAB where F1573 = '$user'";

  $result = $conn->query($query);
  if(!$result)
    return false;

  $row=$result->fetch_array();
  return trim($row[0]);
}

// get the type (HTML or Text) that this person wants user in
function get_mimetype($user)
{
  if(!$user)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "select mimetype from subscribers where user = '$user'";

  $result = $conn->query($query);
  if(!$result)
    return false;
  $row=$result->fetch_array();
  return trim($row[0]);
}

function get_email($user)
{
	
}


// subscribe this email address to this list
function subscribe($email, $listid)
{
	//var_dump($email);
  /*if(!$listid||!list_exists($listid))
  {
	  echo "<p>Something not right</p>";
    return false;
  }*/

  //if already subscribed exit
  if(subscribed($email, $listid))
  {
	 echo "<p>Already exists</p>"; 
    return false;
  }

  if(!($conn=db_connect()))
  {
	echo "<p>Could not connect to database</p>";
    return false;
  }

  $query = "insert into sub_lists(email,listid) values ('$email', $listid)";

  if($conn = db_connect())
  {
	  if($conn->query($query))
		  return true;
	  else
		  return false;
  }
  
  else
  {
	  echo "<p>Could not store;</p>";
	  return false;
  }
}

// unsubscribe this user address from this list
function unsubscribe($user, $listid)
{
  if(!$user||!$listid)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "delete from sub_lists where email = '$user' and listid = $listid";

  $result = $conn->query($query);
  return $result;
}

function get_subscribers($listid)
{
	$list = array();
	
	if(!($conn=db_connect()))
  {
	echo "<p>Could not connect to database</p>";
    return false;
  }
  
  $query = "select email from sub_lists where listid = '$listid'";
  
	$result = $conn->query($query);
  if(!$result)
    return false;
    
   $num = $result->num_rows;
    
	for($i = 0; $i < $num; $i++)
	{
      $list[$i] = $result->fetch_array();
	}
 
	return $list;
	
	
}

// load the data stored about this mail from the database
function load_mail_info($mailid)
{
  if(!$mailid)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "select subject, listid, status, sent from mail
            where mailid = $mailid";

  $result = $conn->query($query);

  if(!$result)
  {
    echo "Cannot retrieve this mail";
    return false;
  }
  return $result->fetch_assoc();

}

// load the data stored about this list from the database
function load_list_info($listid)
{
  if(!$listid)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "select listname, blurb from lists where listid = $listid";
  $result = $conn->query($query);
  if(!$result)
  {
    echo 'Cannot retrieve this list';
    return false;
  }
  $info =  $result->fetch_assoc();

  $query = "select count(*) from sub_lists where listid = $listid";
  $result = $conn->query($query);
  if($result)
  {
    $row = $result->fetch_array();
    $info['subscribers'] = $row[0];
  }
  $query = "select count(*) from mail where listid = $listid
            and status = 'SENT'";
  $result = $conn->query($query);
  if($result)
  {
    $row = $result->fetch_array();
    $info['archive'] = $row[0];
  }
  return $info;
}


// get the name that belongs to this list id
function get_list_name($listid)
{
  if(!$listid)
    return false;

  if(!($conn=db_connect()))
    return false;

  $query = "select listname from lists where listid = $listid";
  $result = $conn->query($query);
  if(!$result)
  {
    return false;
  }
  $row = $result->fetch_array();
  return $row[0];
}

// add a new list to the database
function store_list($admin_user, $details)
{
  if(!filled_out($details))
  {
    echo 'All fields must be filled in.  Try again.<br /><br />';
    return false;
  }
  else
  {
    if(!check_admin_user($admin_user))
      return false;  
      // how did this function get called by somebody not logged in as admin?
    
    if(!($conn=db_connect()))
    { 
      return false;
    }
    
    $query = "select count(*) from lists where listname = '".$details['name']."'";
    $result = $conn->query($query);
	if (!$result)
		return false;

    $row = $result->fetch_array();
    if($row[0] > 0)
    {
      echo 'Sorry, there is already a list with this name.';
      return false;
    }
    
    $query = "insert into lists values (NULL, 
                                       '".$details['name']."',
                                       '".$details['blurb']."')";

    $result = $conn->query($query);
    return $result; 
  }
}

// get the lists that this user is subscribed to
function get_subscribed_lists($user)
{
  $list = array();  

  $query = "select lists.listid, listname from sub_lists, lists 
            where email='$user' and lists.listid = sub_lists.listid 
            order by listname";

  if($conn=db_connect())
  {
    $result = $conn->query($query);
    if(!$result)
    {
      echo '<p>Unable to get list from database.</p>';
      return false;
    }
    $num = $result->num_rows;
    for($i = 0; $i<$num; $i++)
    {
      $row = $result->fetch_array();
      array_push($list, array($row[0], $row[1]));
    }
  }
  return $list;
}

//get the lists that this user is *not* subscribed to 
function get_unsubscribed_lists($user)
{
  $list = array();  

  $query = "select lists.listid, listname, user from lists left join sub_lists 
                   on lists.listid = sub_lists.listid 
                   and user='$user' where user is NULL order by listname";
  if($conn=db_connect())
  {
    $result = $conn->query($query);
    if(!$result)
    {
      echo '<p>Unable to get list from database.</p>';
      return false;
    }
    $num = $result->num_rows;
    for($i = 0; $i<$num; $i++)
    {
      $row = $result->fetch_array();
      array_push($list, array($row[0], $row[1]));
    }
  }
  return $list;
}

// get all lists 
function get_all_lists()
{
  $list = array();  

  $query = 'select listid, listname from lists order by listname';

  if($conn=db_connect())
  {
    $result = $conn->query($query);
    if(!$result)
    {
      echo '<p>Unable to get list from database.</p>';
      return false;
    }
    $num = $result->num_rows;
    for($i = 0; $i<$num; $i++)
    {
      $row = $result->fetch_array();
      array_push($list, array($row[0], $row[1]));
    }
  }
  return $list;
} 

function get_archive($listid)
{
  //returns an array of the archived mail for this list
  //array has rows like (mailid, subject)

  $list = array();  
  $listname = get_list_name($listid);
  
  $query = "select mailid, subject, listid from mail 
            where listid = $listid and status = 'SENT' order by sent"; 

  if($conn=db_connect())
  {
    $result = $conn->query($query);
    if(!$result)
    {
      echo '<p>Unable to get list from database.</p>';
      return false;
    }
    $num = $result->num_rows;
  
    for($i = 0; $i<$num; $i++)
    {
      $row = $result->fetch_array();
      $arr_row = array($row[0], $row[1], 
                   $listname, $listid); 
      array_push($list, $arr_row);
    }
  }
  return $list;
} 

// get the list of mail created, but not yet sent
function get_unsent_mail($user)
{
  if(!check_admin_user($user))
  {
    return false;
  }
  
  $list = array();  

  $query = "select mailid, subject, listid from mail 
            where status = 'STORED' or status = 'TESTED' order by modified"; 

  if($conn=db_connect())
  {
    $result = $conn->query($query);
    if(!$result)
    {
      echo '<p>Unable to get list from database.</p>';
      return false;
    }
    $num = $result->num_rows;
    for($i = 0; $i<$num; $i++)
    {
      $row = $result->fetch_array();
      array_push($list, array($row[0], 
                              $row[1], 
                              get_list_name($row[2]),
                              $row[2]
                              )
                 );
    }
  }
  return $list;
} 

// add a new subscriber to the database, or let a user modify their data
function store_account($normal_user, $admin_user, $details)
{
  if(!filled_out($details))
  {
    echo 'All fields must be filled in.  Try again.<br /><br />';
    return false;
  }
  else
  {
    if(subscriber_exists($details['user']))
    {
      //check logged in as the user they are trying to change
      if(get_user()==$details['user'])
      {
        $query = "update subscribers set realname = '$details[realname]',
                                         mimetype = '$details[mimetype]'
                  where user = '" . $details[user] . "'";
        if($conn=db_connect())
        {
          if ($conn->query($query))
            return true;
          else
            return false;
        }
        else
        {
          echo 'Could not store changes.<br /><br /><br /><br /><br /><br />';
          return false;
        }
      }
      else
      {
        echo '<p>Sorry, that email address is already registered here.</p>';
        echo '<p>You will need to log in with that address to change '
             .' its settings.</p>';
        return false;                 
      }      
    }
    else // new account
    {
      $query = "insert into subscribers 
                        values ('$details[user]',
                        '$details[realname]',
                        '$details[mimetype]',
                         sha1('$details[new_password]'),
                                                0)";          
        if($conn=db_connect())
        {
          if ($conn->query($query))
            return true;
          else
            return false;
        }
        else
        {
          echo 'Could not store new account.<br /><br /><br /><br /><br /><br />';
          return false;
        }
    }
  }
}

function new_account($normal_user, $admin_user, $details)
{
	
}

// create the message from the stored DB entries and files
// send test messages to the administrator, or real messages to the whole list
function send($mailid, $admin_user, $emailAddr)
{
	
  if(!check_admin_user($admin_user))
    return false;
  
  if(!($info = load_mail_info($mailid)))
  { 
    echo "Cannot load list information for message $mailid";
    return false;
  }
  $subject = $info['subject'];//$CI->email->subject($subject);
  $listid = $info['listid'];
  $status = $info['status'];
  $sent = $info['sent'];
    
  $from_name = 'Trimart';
      
  $from_address = 'jamalbutcher@gmail.com';//$CI->email->from($from_address,$from_name);
  $query = "select email from sub_lists where listid =".$listid;
  
  $conn = db_connect();
  $result = $conn->query($query);
  if (!$result)
  {
    echo "No result";
    return false;  
  }
  else if ($result->num_rows==0)
  {
    echo "There is nobody subscribed to list number $listid";
    return false; 
  }
  
	$mail = new PHPMailer();
	$mail->IsSMTP(); // we are going to use SMTP
	$mail->SMTPAuth   = true; // enabled SMTP authentication
	$mail->SMTPSecure = "ssl";  // prefix for secure protocol to connect to the server
	$mail->Host       = "smtp.gmail.com";//"";192.168.20.202    // setting GMail as our SMTP server
	$mail->Port       = 465;//;8089                   // SMTP port to connect to GMail
	$mail->Username   = "jamalbutcher@gmail.com";//"andre.campbell#mcalbds";  // user email address
	$mail->Password   = "P4m266a-mlx";//"AC#220991";            // password in GMail

	// read in the text version of the newsletter
	$textfilename = APPPATH."archive\\".$listid."\\".$mailid."\\text.txt";
	if(file_exists($textfilename))
	{
		$tfp = fopen($textfilename, "r");
		$text = fread($tfp, filesize($textfilename));
		fclose($tfp);
	}

	// read in the HTML version of the newsletter
	$htmlfilename = APPPATH."archive\\".$listid."\\".$mailid."\\index.html";
	if(file_exists($htmlfilename))
	{
		$hfp = fopen($htmlfilename, "r");
		$html = fread($hfp, filesize($htmlfilename));
		fclose($hfp);
	}


	// get the list of images that relate to this message
	$query = "select path, mimetype from images where mailid = $mailid";
	$result = $conn->query($query);
	if(!$result)
	{
		echo '<p>Unable to get image list from database.</p>';
		return false;
	}
	$num = $result->num_rows;
	for($i = 0; $i<$num; $i++)
	{  
		//load each image from disk
		$row = $result->fetch_array();
		$imgfilename = APPPATH."archive\\$listid\\$mailid\\".$row[0];
		$imgtype = $row[1];
		// add each image to the object
		//$message->addHTMLImage($imgfilename, $imgtype, $imgfilename, true);
		$mail->AddEmbeddedImage($imgfilename);      // some attached files
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // as many as you want

	}
    
  // create message body
  //$body = $message->get();  

  // create message headers
  $from = '"'.get_real_name($admin_user).'" <'.$admin_user.'>';
  $hdrarray = array(              
               'From'    => $from,
               'Subject' => $subject);

  //$hdrs = $message->headers($hdrarray);

 
  if($status == 'STORED')
  {
	$mail->isHTML(true);
    $mail->Subject    = $subjectf;
	$mail->AddAddress($_POST['emailAddr'], $admin_user);
	$sent = FALSE;
	
    // send the HTML message to the administrator
    //$sender->send($admin_user, $hdrs, $body);
	if($html)
	{
		$mail->Body = $html;
		if(!$mail->Send())
		{
			echo "Error sending html version: " . $mail->ErrorInfo;
		}
	}
	else
	{
		$sent = TRUE;
	}
	

	if($sent)
	{
		echo "Mail sent to $admin_user with email address ".$_POST['emailAddr'];
	
		// mark newsletter as tested
		$query = "update mail set status = 'TESTED' where mailid = $mailid";
		$result = $conn->query($query);
		
		echo '<p>Press send again to send mail to whole list.<center>';
		display_button('send', "&id=$mailid");
		echo '</center></p>';
	}
	
	//$mail->ClearAddresses();
	
  }    
  else if($status == 'TESTED') //send to whole list
  {
    $count = 0;
	$subscribers = get_subscribers($_GET['id']);
	
	$CI = & get_instance();
   
	$row = $CI->trimart->get_login_credentials($user,$password);
  
	// for each subscriber
	/*for($i = 0; $i < count($subscribers); $i++)
	{
		
		$mail->AddAddress(subscriber[$i][0],$CI->trimart->get_real_name(subscriber[$i][0]));		
	}*/

	$mail->AddAddress('jamalbutcher2@gmail','YOU');
	$mail->send();
      
    $query = "update mail set status = 'SENT', sent = now() 
              where mailid = $mailid";
    $result = $conn->query($query);
    echo "<p>A total of $count messages were sent.</p>";
    
  }
  else if($status == 'SENT')
  {
    echo '<p>This mail has already been sent.</p>'; 
  }
}
?>