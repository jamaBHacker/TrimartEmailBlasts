<?php

class Mlm extends CI_Model
{
	public function __construct()
	{
		$this->load->database('test2');
	}

	
	public function get_list_subscribers()
	{
		$result = $this->db->query("select F1573 from STORESQL.dbo.CLT_TAB where F1573 != 'NULL' and F1573 != ''")->result_array();
		
		return $result;
	}


}

?>