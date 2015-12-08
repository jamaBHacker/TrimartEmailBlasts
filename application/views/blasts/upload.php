<?php
  // this functionality is in a separate file to allow us to be
  // more paranoid with it
  
  // if anything goes wrong, we will exit 

  $max_size = 5000000;
  
  session_start();

  // only admin users can upload files
  if(!check_admin_user())
  {
    echo 'You do not seem to be authorized to use this page.';
    exit;
  }
  
  // set up the admin toolbar buttons
  $buttons = array();
  $buttons[0] = 'change-password';
  $buttons[1] = 'create-list';
  $buttons[2] = 'create-mail';
  $buttons[3] = 'view-mail';
  $buttons[5] = 'log-out';
  $buttons[4] = 'show-all-lists';
  //$buttons[6] = 'show-my-lists';
  //$buttons[7] = 'show-other-lists';
  
  do_html_header('Trimart - Upload Files');
  
  display_toolbar($buttons);

  // check that the page is being called with the required data  //!
  if((!$_FILES['userfile']['name'][0]&&!$_FILES['userfile']['name'][1])
     ||!$_POST['subject']||!$_POST['list'])
 
  {
      echo 'Problem: You did not fill out the form fully. The images are the 
            only optional fields.  Each message needs a subject, and a text version 
            or an HTML version.';
      do_html_footer();
      exit;
  }
  $list = $_POST['list'];
  $subject = $_POST['subject'];

  if(!($conn=db_connect()))
  {
     echo '<p>Could not connect to db</p>'; 
     do_html_footer();
     exit;
  }
  
  // add mail details to the DB  
  $query = "insert into mail values (NULL, 
                                     '".$_SESSION['admin_user']."',
                                     '".$subject."',
                                     '".$list."',
                                     'STORED', NULL, NULL)";
  $result = $conn->query($query);
  if(!$result)   
  { 
	echo "<p>No results to display</P>";
    do_html_footer();  
    exit; 
  }
  
  //get the id MySQL assigned to this mail
  $mailid = $conn->insert_id;
        
  if(!$mailid)   
  { 
    do_html_footer();  
    exit; 
  }
  
  
  // creating directory will fail if this is not the first message archived  
  // that's ok
  @ mkdir(APPPATH.'archive\\'.$list, 0777);
	
  // it is a problem if creating the specific directory for this mail fails 
  if(!mkdir(APPPATH.'archive\\'.$list."\\$mailid", 0777, TRUE))
  { 
	echo APPPATH.'archive\\'.$list."\\$mailid";
	die("Failed to create folders");
    exit; 
  }
  
  
  // iterate through the array of uploaded files
  $i = 0;
  while ($i <= count($_FILES))
  {
	if(!empty($_FILES['userfile']['name'][$i]))
	{
		echo '<p>Uploading '.$_FILES['userfile']['name'][$i].' - ';
		echo $_FILES['userfile']['size'][$i].' bytes.</p>';
	}
	
	else
	{
		$i++;
		continue;
	}
	
	
    if ($_FILES['userfile']['size'][$i]==0)
    {
      echo 'Problem: '.$_FILES['userfile']['name'][$i].
           ' is zero length';
      $i++;
      continue;  
    }
  
    if ($_FILES['userfile']['size'][$i]>$max_size)
    {
      echo 'Problem: '.$_FILES['userfile']['name'][$i].' is over '
            .$max_size.' bytes';
      $i++;
      continue;  
    }

    // we would like to check that the uploaded image is an image
    // if getimagesize() can work out its size, it probably is.
    if($i>1&&!getimagesize($_FILES['userfile']['tmp_name'][$i]))
    {
      echo 'Problem: '.$_FILES['userfile']['name'][$i].
           ' is corrupt, or not a gif, jpeg or png';
      $i++;
      continue;  
    }
  
    // file 0 (the text message) and file 1 (the html message) are special cases
    if($i==0) 
	{
      $destination = APPPATH."archive\\$list\\$mailid\\text.txt";
	}
    else if($i == 1)
	{
      $destination = APPPATH."archive\\$list\\$mailid\\index.html";
	}
    else
    {
      $destination = APPPATH."archive\\$list\\$mailid\\"
                     .$_FILES['userfile']['name'][$i];
      $query = "insert into images values ($mailid, 
                             '".$_FILES['userfile']['name'][$i]."',
                             '".$_FILES['userfile']['type'][$i]."')";
      $result = $conn->query($query);
    }
    //if we are using PHP version >= 4.03

    if (!is_uploaded_file($_FILES['userfile']['tmp_name'][$i]))
    { 
      // possible file upload attack detected
      echo 'Somet
	  hing funny happening with '
           .$_FILES['userfile']['name'].', not uploading.';
      do_html_footer();
      exit;
    }
    
    move_uploaded_file($_FILES['userfile']['tmp_name'][$i], 
                       $destination);
    
    $i++;
  }
  
  //display_preview_button($list, $mailid, 'preview-html');
  //display_preview_button($list, $mailid, 'preview-text');
  echo "<form method='post' action='".base_url()."index.php?action=send&id=".$mailid."'>"; //"&emailAddr=".$emailAddr
  echo "<div class='form-group'>
			<label for='emailAddr' control-label'>Enter email address for test email</label>
			<input class='form-control' type='text' name='emailAddr'/>
			<input class='form-control' type='submit' value='send'/></div></form>";
  //display_button('send', "&id=$mailid");
  
  echo '<br /><br /><br /><br /><br />';
  do_html_footer();
?>  
