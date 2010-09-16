<?php
class Index extends Controller {

    function __construct() {
        parent::Controller();

		// load helpers and libraries
		$this->load->helper('url');
		$this->load->library('twitter');
		$this->load->library('chirpchirp');
    }

	/**
	* Not logged in; show latest trends list.
	*/
	function index() {
		$trends = $this->twitter->search('trends');

		$data = array(
			'base_url' => base_url(),
			'trends' => $trends->trends,
		);

		$this->load->view('index', $data);
		
	}
	
}
?>