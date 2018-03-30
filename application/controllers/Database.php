<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dbpracas');
    }

	public function index()
	{
		//$this->dbpracas->init();

		$this->load->view('database');
	}
}
