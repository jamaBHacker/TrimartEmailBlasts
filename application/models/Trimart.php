<?php

class Trimart extends CI_Model
{
	public function __construct()
	{
		$this->load->database('test3');
	}

	
	public function get_login_credentials($user,$password)
	{						 
		$result = $this->db->query("select F1142 from STORESQL.dbo.CLK_TAB where F1143='$user' and F1141='$password'")->row_array();
		
		return $result;
	}
	
	public function get_addresses()
	{
		$result = $this->db->query("select F1155,F1573 from STORESQL.dbo.CLT_TAB where F1573 != 'NULL' and F1573 != '' ORDER BY F1155 ASC")->result_array();
	
		return $result;
	}
	
	public function get_subscribers()
	{
		$result = $this->db->query()->result_array();
	}
	
	public function get_real_name($user)
	{
		$row = $this->db->query("select F1155 from STORESQL.dbo.CLK_TAB where F1573 = '$user'")->row_array();
		return $row['F1155'];
	}


}


//select F1127,F1141 from dbo.CLK_TAB where F1142 >= 5; //used to select login credentials
//SELECT F1142 FROM dbo.CLK_TAB WHERE F1143 ='$user' AND F1141 ='$password'




?>