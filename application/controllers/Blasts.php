<?php

class Blasts extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		ini_set('MAX_EXECUTION_TIME', 60);
		$this->load->model('trimart');
		$this->load->library('My_PHPMailer');
		$this->load->library('email');
		$this->load->helper( 'url' );
		$this->load->helper( array('db_fns', 'data_valid_fns', 'output_fns', 'mlm_fns', 'user_auth_fns') );
		error_reporting( E_ALL ^ E_NOTICE );
		
	}
	
	/*public function index( $page = 'index' )
	 {
		if ( ! file_exists(APPPATH.'/views/blasts/'.$page.'.php') )
        {
                // Whoops, we don't have a page for that!
                show_404();
        }

        $data['title'] = ucfirst($page); // Capitalize the first letter

        $this->load->view('templates/header');
        $this->load->view('blasts/'.$page);
        //$this->load->view('templates/footer');
	 
	 }*/
	
	 public function view( $page = 'index' )
	 {
		if ( !file_exists(APPPATH.'/views/blasts/'.$page.'.php'))
        {
                // Whoops, we don't have a page for that!
                show_404();
        }

        $data['title'] = ucfirst($page); // Capitalize the first letter

        $this->load->view('templates/header');
        $this->load->view('blasts/'.$page);
        //$this->load->view('templates/footer');
	 
	 }
	 
}
?>