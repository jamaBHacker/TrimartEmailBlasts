<?php

?>
  
  <body>
	<div class = "container-fluid">
	
	<div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Dropdown Example
  <span class="caret"></span></button>
  <ul class="dropdown-menu">
    <li><a href="#">HTML</a></li>
    <li><a href="#">CSS</a></li>
    <li><a href="#">JavaScript</a></li>
  </ul>
</div>
  
<?php
	$list = array();
  $list = address_book();
  
  for($i = 0; $i < count($list); $i++)
{
	?>
	<div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?php echo $list[$i][1] ?>
  <span class="caret"></span></button>
  <ul class="dropdown-menu">
  
  <?php
	echo "<li><a href='#'></a></li>".$list[$i][0]."</ul></div>";
										
}

?>

</div>
</body>
</html>

?>