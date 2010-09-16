<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Chirpchirp {
    function __construct() {
		$this->CI =& get_instance();

		// load helpers and libraries
		$this->CI->load->helper('url');
		$this->CI->load->helper('form');
		$this->CI->load->library('session');
		$this->CI->load->library('twitter');

		// Twitter oauth client info
		$this->consumer_key = 'Gv4dsLXvG7nWp6kLGJqjmg';
		$this->consumer_key_secret = '1pQCLjPXjEUSSKpuNMnjZlYxfNYTgwwUb0Vlm1GlmTw';
		
		$this->tokens['access_token'] = NULL;
		$this->tokens['access_token_secret'] = NULL;

		$this->has_access = FALSE;
		$this->check_for_access();
		//echo $this->has_access;
		
		$this->uri_string = uri_string();
		if ((uri_string() == '') && $this->has_access) {
			header('Location: ' . site_url('user/timeline'));
		}
		if (strrpos(uri_string(), 'user')) {
			$this->oauth = $this->init_oauth();
		}
		
    }

	function check_for_access() {
		if (($this->CI->session->userdata('access_token') !== FALSE) && ($this->CI->session->userdata('access_token_secret') !== FALSE)) {
			$this->has_access = TRUE;
		}
	}

	/**
	* Get access token and access token secret from session, if they're there.
	*/
	function init_oauth() {
		if ($this->has_access === TRUE) {
			$this->tokens['access_token'] = $this->CI->session->userdata('access_token');
			$this->tokens['access_token_secret'] = $this->CI->session->userdata('access_token_secret');			
		}
		$oauth = $this->CI->twitter->oauth($this->consumer_key, $this->consumer_key_secret, $this->tokens['access_token'], $this->tokens['access_token_secret']);
		return $oauth;
	}

}

?>
