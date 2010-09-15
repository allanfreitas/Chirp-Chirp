<?php
class User extends Controller {

    function __construct() {
        parent::Controller();

		// load helpers and libraries
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('twitter');
		$this->load->library('chirpchirp');

    }


	/**
	* Authenticate with Twitter and store access token and token secret in session.
	*/
	function index() {

		// Get and save access_token and access_token_secret in session data
		// TO DO: use a database
		
		//$oauth = $this->chirpchirp->init_oauth();

		if (isset($this->chirpchirp->oauth['access_token']) && isset($this->chirpchirp->oauth['access_token_secret'])) {
			// SAVE THE ACCESS TOKENS	
			$this->session->set_userdata('access_token', $this->chirpchirp->oauth['access_token']);
			$this->session->set_userdata('access_token_secret', $this->chirpchirp->oauth['access_token_secret']);
			if (isset($_GET['oauth_token'])) {
				// Redirect the user since we've saved their authentication
				header('Location: ' . site_url('user/timeline'));
				return;
			}
		}
	}

	/**
	* Display authenticated user's timeline.
	*/
	function timeline() {

		//$oauth = $this->chirpchirp->init_oauth();

		$timeline = $this->twitter->call('statuses/user_timeline');
		
		$data = array(
			'base_url' => base_url(),
			'user_name' => $timeline[0]->user->name,
			'user_screen_name' => $timeline[0]->user->screen_name,
			'timeline' => $timeline
		);

		$this->load->view('timeline', $data);
	}

}
?>