<?php defined('SYSPATH') or die('No direct script access.');
/**
 * NemoAPI - Base class for all nemoAPI classes, this layer is responsible for
 * taking care of making HTTP requests to the Nemo API and handling the
 * responses. Also implements automatic handling of getters/setters for all
 * properties.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @uses		Request_Client_External
 * @uses		Request
 * @abstract
 */
abstract class NemoAPI
{
	/**
	 * NemoAPI::STATUS_SUCCESS - value for a successful response from the Nemo API
	 *
	 * @static
	 */
	const STATUS_SUCCESS = 'success';

	/**
	 * The URI of the Nemo API, this is where all requests will be sent, declared
	 * as a var rather than a const so it can be overridden in each implementation
	 *
	 * @staticvar	string	$nemo_api_uri
	 * @access		protected
	 */
	protected static $nemo_uri = 'https://nemo.waterbabies.co.uk';

	/**
	 * The Nemo API key, declared as a var rather than a constant so it can be
	 * overridden in each implementation
	 *
	 * @staticvar	string		$nemo_api_uri
	 * @access		protected
	 */
	protected static $nemo_api_key = 'd555GGhh6fdSqbbgjl38F5hJ3dCv337$vVfssjhjE$4D';

	/**
	 * Constructor
	 *
	 * @param	array	$inflate_data	The data to inflate the object with
	 * @access	public
	 */
	public function __construct(Array $inflate_data = NULL)
	{
		if($inflate_data !== NULL)
		{
			$this->inflate($inflate_data);
		}
	}

	/**
	 * Makes a HTTP GET request to the specified path of the Nemo API with the
	 * specified parameters
	 *
	 * @param	string		$path		The Nemo API path where the request should be sent
	 * @param	array		$params		Any extra request parameters to be included
	 * @return	array					The response data
	 * @access	protected
	 * @static
	 */
	protected static function get($path, Array $params = array())
	{
		return self::request(Request::GET, $path, $params);
	}

	/**
	 * Makes a HTTP POST request to the specified path of the Nemo API with the
	 * specified parameters
	 *
	 * @param	string		$path		The Nemo API path where the request should be sent
	 * @param	array		$params		Any extra request parameters to be included
	 * @return	array					The response data
	 * @access	protected
	 * @static
	 */
	protected static function post($path, Array $params = array())
	{
		return self::request(Request::POST, $path, $params);
	}

	/**
	 * Makes a HTTP request of the specified type to the specified path of the
	 * Nemo API with the specified parameters
	 *
	 * @param	string		$method		The HTTP request type, should be one of 'GET' or 'POST'
	 * @param	string		$path		The Nemo API path where the request should be sent
	 * @param	array		$params		Any extra request parameters to be included
	 * @return	array					The response data
	 * @access	private
	 * @throws	Exception
	 * @static
	 */
	private static function request($method, $path, Array $params = array())
	{
		// full URI is the API URI plus the specified path
		$uri = self::get_uri().'/api'.$path;

		// build the request object
		$request = Request::factory($uri);

		// always add the api key as a query string parameter
		$request->query('api_key', self::$nemo_api_key);

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
			throw new Exception('Unknown request method');
		}
		$request->method($method);

		// execute the request
		$request_client = new Request_Client_External;
		$response = $request_client->execute($request);

		// return the data from the response
		return self::parse_response($response);
	}

	/**
	 * Parses a response from the Nemo API and returns the results as an array,
	 * if the response contains any errors an exception is thrown
	 *
	 * @param	Response			$response
	 * @return	array
	 * @throws	NemoAPI_Exception
	 * @static
	 */
	private static function parse_response(Response $response)
	{
		if ($response->status() >= 400)
		{
			throw new NemoAPI_Exception('HTTP '.$response->status().' error');
		}

		$parsed_result = json_decode($response->body(), TRUE);

		// check for errors
		if(isset($parsed_result['errors']))
		{
			throw new NemoAPI_Exception(implode(',', $parsed_result['errors']));
		}

		return $parsed_result;
	}

	/**
	 * Static getter for the $nemo_uri static var
	 *
	 * @static
	 * @access	public
	 */
	public static function get_uri()
	{
		return self::$nemo_uri;
	}

	/**
	 * Static setter for the $nemo_uri static var
	 *
	 * @param	string	$uri
	 * @static
	 * @access	public
	 */
	public static function set_uri($uri)
	{
		self::$nemo_uri = $uri;
	}

	/**
	 * Adds all data from the input array to the object via setters, designed
	 * as a quick way to build the object from data returned via the API. The
	 * keys of the input array must match the property names of the class.
	 *
	 * @param	array	$data
	 * @access	public
	 */
	public function inflate(Array $data)
	{
		foreach($data as $property_name => $value)
		{
			$method_name = 'set_'.$property_name;
			$this->{$method_name}($value);
		}
	}

	/**
	 * Magic __call method to catch all calls to undefined/non-visible
	 * functions.  Used to automatically create getters and setters for all
	 * properties.  If this method catches a call for a method name that does
	 * not being with get_ or set_ an Exception will be thrown.
	 *
	 * @param	string	$name
	 * @param	array	$arguments
	 * @return	mixed
	 * @access	public
	 * @throws	Exception
	 */
	public function __call($name, $arguments)
	{
		$getter_regex_pattern = '/get_([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*.)/';
		$setter_regex_pattern = '/set_([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*.)/';
		$matches = array();

		if(preg_match($getter_regex_pattern, $name, $matches))
		{
			$property_name = $matches[1];

			if(!(property_exists($this, $property_name)))
			{
				throw new Exception('Attempted to get undefined property: '.$property_name);
			}

			return $this->$property_name;
		}
		elseif(preg_match($setter_regex_pattern, $name, $matches))
		{
			$property_name = $matches[1];
			$value = $arguments[0];

			if(!(property_exists($this, $property_name)))
			{
				throw new Exception('Attempted to set undefined property: '.$property_name);
			}

			$this->$property_name = $value;
		}
		else
		{
			throw new Exception('Call to undefined method: '.get_class($this).'::'.$name);
		}
	}

}