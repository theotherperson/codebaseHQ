<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase Request Core - Creates and executes a valid Codebase API request and
 * returns the response.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @uses		Request
 */
class Codebase_Request_Core
{

	/**
	 * The Codebase API URI
	 *
	 * @var		string
	 * @access	private
	 */
	private $api_uri = NULL;

	/**
	 * The Codebase username in the format of account/username
	 *
	 * @var		string
	 * @access	private
	 */
	private $username = NULL;

	/**
	 * The Codebase API key
	 *
	 * @var		string
	 * @access	private
	 */
	private $api_key = NULL;

	/**
	 * A cache object to be used by any requests generated, can be overriden
	 * when the request is made
	 *
	 * @var		HTTP_Cache
	 * @access	private
	 */
	private $cache = NULL;

	/**
	 * Constructor
	 *
	 * @param	string		$api_uri	The Codebase API URI
	 * @param	string		$username	The Codebase username in the format of account/username
	 * @param	string		$api_key	The Codebase API key
	 * @param	HTTP_Cache	$cache		A HTTP_cache instance that will be used by any requests made to the Codebase API, can be overriden at the time of request
	 * @access	public
	 */
	public function __construct($api_uri, $username, $api_key, HTTP_Cache $cache = NULL)
	{
		$this->set_api_uri($api_uri);
		$this->set_username($username);
		$this->set_api_key($api_key);
		$this->set_cache($cache);
	}

	/**
	 * Makes a HTTP GET request to the specified path of the Codebase API with the
	 * specified parameters
	 *
	 * @param	string		$path		The Codebase API path where the request should be sent
	 * @param	array		$params		Any extra request parameters to be included
	 * @param	mixed		$cache		A HTTP_Cache object to be used for this request or boolean FALSE if no caching should be used
	 * @return	array					The response data
	 * @access	public
	 */
	public function get($path, Array $params = array(), $cache = NULL)
	{
		return $this->request(Request::GET, $path, $params, $cache);
	}

	/**
	 * Makes a HTTP POST request to the specified path of the Codebase API with the
	 * specified parameters
	 *
	 * @param	string		$path		The Codebase API path where the request should be sent
	 * @param	array		$params		Any extra request parameters to be included
	 * @param	mixed		$cache		A HTTP_Cache object to be used for this request or boolean FALSE if no caching should be used
	 * @return	array					The response data
	 * @access	public
	 */
	public function post($path, Array $params = array(), $cache = NULL)
	{
		return $this->request(Request::POST, $path, $params, $cache);
	}

	/**
	 * Makes a HTTP request of the specified type to the specified path of the
	 * Codebase API with the specified parameters
	 *
	 * @param	string		$method		The HTTP request type, should be one of 'GET' or 'POST'
	 * @param	string		$path		The Codebase API path where the request should be sent
	 * @param	array		$params		Any extra request parameters to be included
	 * @param	mixed		$cache		A HTTP_Cache object to be used for this request or boolean FALSE if no caching should be used
	 * @return	array					The response data
	 * @access	protected
	 * @throws	Exception
	 */
	protected function request($method, $path, Array $params = array(), $cache = NULL)
	{
		// full URI is the Codebase API URI plus the specified path
		$uri = $this->get_api_uri().$path;

		/*
		 * check for a FALSE cache parameter, this indicates that the cache
		 * shouldn't be used for this request, otherwise if a cache parameter
		 * hasn't been passed, use the default cache object
		 */
		if($cache === FALSE)
		{
			$cache = NULL;
		}
		else if($cache === NULL)
		{
			$cache = $this->get_cache();
		}
		else if(!$cache instanceOf HTTP_Cache)
		{
			throw new Codebase_Exception('cache parameter should be FALSE or instance of HTTP_Cache');
		}

		// build the request object
		$request = Request::factory($uri, $cache);

		// add the basic auth headers
		$request->headers('Authorization', base64_encode($this->get_username().':'.$this->get_api_key()));

		// set the correct MIME types
		$xml_mime_type = 'application/xml';
		$request->headers('Accept', $xml_mime_type);
		$request->headers('Content-type', $xml_mime_type);

		if($method == Request::GET)
		{
			$request->query($params);
		}
		elseif($method == Request::POST)
		{
			$request->post($params);
		}
		else
		{
			throw new Codebase_Exception('Unknown request method');
		}
		$request->method($method);

		// return the response
		return $request->execute();
	}

	/**
	 * Getter for the $api_uri property
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_api_uri()
	{
		return $this->api_uri;
	}

	/**
	 * Setter for the $api_uri property
	 *
	 * @access	public
	 * @param	string	$api_uri	The Codebase API URI
	 */
	public function set_api_uri($api_uri)
	{
		$this->api_uri = $api_uri;
	}

	/**
	 * Getter for the $username property
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_username()
	{
		return $this->username;
	}

	/**
	 * Setter for the $username property
	 *
	 * @access	public
	 * @param	string	$username	The Codebase username
	 */
	public function set_username($username)
	{
		$this->username = $username;
	}

	/**
	 * Getter for the $api_key property
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_api_key()
	{
		return $this->api_key;
	}

	/**
	 * Setter for the $api_key property
	 *
	 * @access	public
	 * @param	string	$api_key	The Codebase API key
	 */
	public function set_api_key($api_key)
	{
		$this->api_key = $api_key;
	}

	/**
	 * Getter for the $cache property
	 *
	 * @access	public
	 * @return	HTTP_Cache
	 */
	public function get_cache() {
		return $this->cache;
	}

	/**
	 * Setter for the $cache property
	 *
	 * @access	public
	 * @param	HTTP_Cache	$cache	The cache object
	 */
	public function set_cache(HTTP_Cache $cache) {
		$this->cache = $cache;
	}

}