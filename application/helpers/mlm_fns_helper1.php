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
  //var_dump($row[0]>0);
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
	$list = array();
	
	$query = "select F1155,F1573 from clt_tab where F1573 is not null and F1573 != '' ORDER BY F1150 ASC";
	
  if($conn=db_connect1())
  {
    $result = $conn->query($query);
    if(!$result)
    {
      echo '<p>Unable to get list from database.</p>';
      return false;
    }
    $num = $result->num_rows;
    
	for($i = 0; $i < $num; $i++)
	{
      $list[$i] = $result->fetch_array();
      //array_push($list, array($row[0]));
	}
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

  $query = "select F1155 from clk_tab where F1573 = '$user'";

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
      //array_push($list, array($row[0]));
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
	/*if (!$result)
		return false;*/

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
	$CI = & get_instance();
	$CI->load->library('email');
	/*$config['protocol'] = "smtp";
	$config['smtp_host'] = "ssl://smtp.gmail.com";
	//$config['smpt_timeout'] = '5';
	//$config['smtp_user'] = "jamalbutcher@gmail.com";
	//$config['smtp_pass'] = "P4m266a-mlx";
	$config['smtp_port'] = 465;//"8089";
	$config['charset'] = 'iso-8859-1';
	$config['mailtype'] = "text";
	$config['newline'] = "\r\n";
	//$config['validation'] = TRUE;*/
	
	 //$config['protocol'] = 'smtp';
    //$config['smtp_host'] = 'aspmx.l.google.com'; //change this
    //$config['smtp_port'] = '25';
    //$config['smtp_user'] = 'jamalbutcher@gmail.com'; //change this
    //$config['smtp_pass'] = 'P4m266a-mlx'; //change this
    //$config['mailtype'] = 'text';
    //$config['charset'] = 'iso-8859-1';
    //$config['wordwrap'] = TRUE;
    //$config['newline'] = "\r\n";

	//$CI->email->initialize($config);
	
	/*$CI->email->from('jamalbutcher@gmail', 'sender name');
    $CI->email->to('jamalbutcher@gmail');
    //$CI->email->cc('test2@gmail.com'); 
    $CI->email->subject('Your Subject');
    $CI->email->message('Your Message');
    //$CI->email->attach('/path/to/file1.png'); // attach file
    //$CI->email->attach('/path/to/file2.pdf');
    if ($CI->email->send())
        echo "Mail Sent!";
    else
        echo "There is error in sending mail!";*/
	
	ini_set('SMTP', 'smtp.gmail.com'); //192.168.20.202
	ini_set('smtp_port', 25); //25
	ini_set('sendmail_from','jamalbutcher@gmail');
	//ini_set('smtp_user','jamalbutcher@gmail');
	//ini_set('smptp_pass','P4m266a-mlx');
	//ini_set('SMTP', '192.168.20.202'); //
	//ini_set('smtp_port', 8089); //25
	
  if(!check_admin_user($admin_user))
    return false;
  
  if(!($info = load_mail_info($mailid)))
  { 
    echo "Cannot load list information for message $mailid";
    return false;
  }
  $subject = $info['subject'];$CI->email->subject($subject);
  $listid = $info['listid'];
  $status = $info['status'];
  $sent = $info['sent'];
    
  $from_name = 'Trimart';
      
  $from_address = 'jamalbutcher@gmail.com';$CI->email->from($from_address,$from_name);
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
  
  // include PEAR mail classes
  //include('Mail.php');
  //include('Mail/Mime.php');

  // instantiate MIME class and pass it the carriage return/line feed 
  // character used on this system
  //$message = new Mail_mime("\r\n");

  // read in the text version of the newsletter
  $textfilename = APPPATH."archive\\".$listid."\\".$mailid."\\text.txt";
  //$tfp = fopen($textfilename, "r");
  //$text = fread($tfp, filesize($textfilename));
  //fclose($tfp);

  // read in the HTML version of the newsletter
  $htmlfilename = APPPATH."archive\\".$listid."\\".$mailid."\\index.html";
  //$hfp = fopen($htmlfilename, "r");
  //$html = fread($hfp, filesize($htmlfilename));
  //fclose($hfp);

  // add HTML and text to the mimuser object
  //$message->setTXTBody($text);
  //$message->setHTMLBody($html);

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
	//var_dump($imgfilename);
    $imgtype = $row[1];
     // add each image to the object
    //$message->addHTMLImage($imgfilename, $imgtype, $imgfilename, true);
	$CI->email->attach($imgfilename);
  }
    
  // create message body
  //$body = $message->get();  

  // create message headers
  $from = '"'.get_real_name($admin_user).'" <'.$admin_user.'>';
  $hdrarray = array(              
               'From'    => $from,
               'Subject' => $subject);

  //$hdrs = $message->headers($hdrarray);


  // create the actual sending object
  //$sender =& Mail::factory('mail');
 
  if($status == 'STORED')
  {
    
    // send the HTML message to the administrator
    //$sender->send($admin_user, $hdrs, $body);
	
      
    // send the plain text version of the message to administrator
    //mail($_POST['emailAddr'], $subject, $text, 'From: "'.$admin_user.'" <'.$admin_user.">");
	//var_dump($_POST['emailAddr']);
	if(mail($_POST['emailAddr'], $subject, 'Hello'))
	{
		echo "<p>Success you genius</p>";
	}
	$CI->email->to($_POST['emailAddr']);

	//var_dump($_POST['emailAddr']);
	if(mail($_POST['emailAddr'], $subject,'Hello',$from_address))
	{
		echo "Mail sent to $admin_user with email address ".$_POST['emailAddr'];
	


		// mark newsletter as tested
		$query = "update mail set status = 'TESTED' where mailid = $mailid";
		$result = $conn->query($query);
		
		echo '<p>Press send again to send mail to whole list.<center>';
		display_button('send', "&id=$mailid");
		echo '</center></p>';
	}
	
	/*else 
	{
		echo $CI->email->print_debugger();
    }*/
  }    
  else if($status == 'TESTED')
  {
    //send to whole list
    
    $query = "select subscribers.realname, sub_lists.email, 
                     subscribers.mimetype  
              from sub_lists, subscribers 
              where listid = $listid and 
                    sub_lists.email = subscribers.email";
                       
    $result = $conn->query($query);
    if(!$result)
      echo '<p>Error getting subscriber list</p>';
      
    $count = 0;      
    // for each subscriber
    while( $subscriber = $result->fetch_row() )
    {
      if($subscriber[2]=='H')
      {
        //send HTML version to people who want it
        //$sender->send($subscriber[1], $hdrs, $body);
		$CI->email->send();
      }
      else
      {
        //send text version to people who don't want HTML mail
        /*mail($subscriber[1], $subject, $text, 
                       'From: "'.get_real_name($admin_user).'" <'.$admin_user.">");*/
		$CI->email->to($subscriber[1]);
		$CI->email->send();
      }
      $count++; 
    }
      
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
