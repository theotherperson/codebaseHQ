<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase Model Core - Base class for all Codebase model classes, this layer
 * is responsible for taking care of making HTTP requests to the Codebase API
 * and handling the responses. Also implements automatic handling of
 * getters/setters for all properties.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @abstract
 */
abstract class Codebase_Model_Core
{

	/**
	 * An instance of Codebase_Request, used to make all requests to the
	 * Codebase API
	 *
	 * @var		Codebase_Request
	 * @access	protected
	 */
	protected $request = NULL;

	/**
	 * Constructor
	 *
	 * @param	array	$inflate_data	The data to inflate the object with
	 * @access	public
	 */
	public function __construct(Codebase_Request $request, SimpleXMLElement $inflate_data = NULL)
	{
		$this->set_request($request);

		if($inflate_data !== NULL)
		{
			$this->inflate($inflate_data);
		}
	}

	/**
	 * Parses a response from the Codebase API and returns the results as an array,
	 * if the response contains any errors an exception is thrown
	 *
	 * @param	Response			$response
	 * @return	array
	 * @throws	Codebase_Exception
	 * @static
	 */
	protected static function parse_response(Response $response)
	{
		if ($response->status() >= 400)
		{
			throw new Codebase_Exception('HTTP '.$response->status().' error');
		}

		$parsed_result = new SimpleXMLElement($response->body());

		// check for errors?

		return $parsed_result;
	}

	/**
	 * Adds all data from the input array to the object via setters, designed
	 * as a quick way to build the object from data returned via the API. The
	 * keys of the input array must match the property names of the class.
	 *
	 * @param	array	$data
	 * @access	public
	 */
	public function inflate(SimpleXMLElement $data)
	{
		$object_element_map = array(
			'status' => 'Codebase_Model_Status',
			'assignee' => 'Codebase_Model_Assignee'
		);
		$elements_to_instantiate = array_keys($object_element_map);

		foreach($data as $property_name => $value)
		{
			$new_value = NULL;

			// Codebase XML element names contain hyphens, replace them with underscores
			$property_name = str_replace('-', '_', $property_name);
			$method_name = 'set_'.$property_name;

			/**
			 *  check to see if the element name exists within the
			 *  object-element map, if it does, create a new instance of the
			 *  mapped object with the element data
			 */
			if(in_array($property_name, $elements_to_instantiate) AND count($value) > 1)
			{
				$new_value = new $object_element_map[$property_name]($this->get_request(), $value);
			}
			else
			{
				$new_value = (string)$value;
			}

			$this->{$method_name}($new_value);
		}
	}

	/**
	 * Magic __call method to catch all calls to undefined/non-visible
	 * functions.  Used to automatically create getters and setters for all
	 * properties.  If this method catches a call for a method name that does
	 * not being with get_ or set_, or attempts to get or set a property that
	 * does not exist, an Exception will be thrown.
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

	/**
	 * static function to return objects of the specified class at the specified
	 * API path, shouldn't be used directly but rather implemented by child
	 * classes.
	 *
	 * @param	Codebase_Request	$request
	 * @param	string				$class_name							The class of the objects to instantiate and return
	 * @param	string				$path								The uri path to make the Codebase API call to
	 * @return	array				A collection Codebase_Model objects
	 * @static
	 * @access	protected
	 */
	protected static function get_objects_for_path(Codebase_Request $request, $class_name, $path)
	{
		// check that the supplied class name is a sub class of Codebase_Model (and therefore this class)
		if(!is_subclass_of($class_name, 'Codebase_Model'))
		{
			throw new Codebase_Exception('The supplied class name must be a sub class of Codebase_Model');
		}

		$objects = array();

		try
		{
			$response = $request->get($path);
			$response_data = self::parse_response($response);

			foreach($response_data as $object_data)
			{
				$objects[] = new $class_name($request, $object_data);
			}
		}
		catch(Codebase_Exception $e)
		{
			// something went wrong with the request
			$objects = array();
		}

		return $objects;
	}

	/**
	 * Getter for the $request property
	 *
	 * @access	public
	 * @return	Codebase_Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Setter for the $request property
	 *
	 * @access	public
	 * @param	Codebase_Request	$request	An instance of Codebase_Request
	 */
	public function set_request(Codebase_Request $request) {
		$this->request = $request;
	}

}