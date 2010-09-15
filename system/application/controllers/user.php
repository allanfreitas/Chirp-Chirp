<?php
class User extends Controller {

    function __construct() {
        parent::Controller();

		// load helpers and libraries
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');
		$this->load->library('twitter');

		// Twitter oauth client info
		$this->consumer_key = 'Gv4dsLXvG7nWp6kLGJqjmg';
		$this->consumer_key_secret = '1pQCLjPXjEUSSKpuNMnjZlYxfNYTgwwUb0Vlm1GlmTw';
		
		$this->tokens['access_token'] = NULL;
		$this->tokens['access_token_secret'] = NULL;

		$this->has_access = FALSE;
    }

	function check_for_access() {
		if (($this->session->userdata('access_token') !== FALSE) && ($this->session->userdata('access_token_secret') !== FALSE)) {
			$this->has_access = TRUE;
		}
	}

	/**
	* Get access token and access token secret from session, if they're there.
	*/
	function init_oauth() {
		$this->check_for_access();
		if ($this->has_access === TRUE) {
			$this->tokens['access_token'] = $this->session->userdata('access_token');
			$this->tokens['access_token_secret'] = $this->session->userdata('access_token_secret');			
		}
		$oauth = $this->twitter->oauth($this->consumer_key, $this->consumer_key_secret, $this->tokens['access_token'], $this->tokens['access_token_secret']);
		return $oauth;
	}

	/**
	* Authenticate with Twitter and store access token and token secret in session.
	*/
	function index() {

		// Get and save access_token and access_token_secret in session data
		// TO DO: use a database
		
		$oauth = $this->init_oauth();

		if (isset($oauth['access_token']) && isset($oauth['access_token_secret'])) {
			// SAVE THE ACCESS TOKENS	
			$this->session->set_userdata('access_token', $oauth['access_token']);
			$this->session->set_userdata('access_token_secret', $oauth['access_token_secret']);
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

		$oauth = $this->init_oauth();
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