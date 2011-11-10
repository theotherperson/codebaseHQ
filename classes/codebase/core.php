<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase Core - Codebase factory class that is responsible for providing
 * instances of all Codebase Model classes.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
abstract class Codebase_Core
{

	/**
	 * Constant for the Codebase API URI, shouldn't be changing often so
	 * hardcoded here
	 */
	const API_URI = 'api3.codebasehq.com';

	/**
	 * An instance of Codebase_Request, used to make all requests to the
	 * Codebase API
	 *
	 * @var		Codebase_Request
	 * @access	private
	 */
	private $request = NULL;

	/**
	 * A collection of Codebase_Model_Objects retrieved by this object
	 *
	 * @var		array
	 * @access	protected
	 */
	protected $projects = NULL;


	/**
	 * Constructor
	 *
	 * @param	string		$username	The Codebase username in the format of account/username
	 * @param	string		$api_key	The Codebase API key
	 * @param	boolean		$secure		True if the connection to the Codebase API should be made over https, false otherwise
	 * @param	HTTP_Cache	$cache		A HTTP_cache instance that will be used by any requests made to the Codebase API
	 * @access	public
	 */
	public function __construct($username, $api_key, $secure = TRUE, HTTP_Cache $cache = NULL)
	{
		$request = new Codebase_Request(self::API_URI, $username, $api_key, $secure, $cache);
		$this->set_request($request);
	}

	/**
	 * Retrieves all projects associated with the codebase account specified by
	 * the credentials in the request object
	 *
	 * @return	array	A collection of Codebase_Model_Project objects
	 */
	public function get_all_projects()
	{
		if($this->projects === NULL)
		{
			$this->projects = Codebase_Model_Project::get_all_projects($this->get_request());
		}

		return $this->projects;
	}

	/**
	 * Retrieves all tickets associated with the codebase account specified by
	 * the credentials in the request object
	 *
	 * @return	array	A collection of Codebase_Model_Ticket objects
	 */
	public function get_all_tickets()
	{
		$tickets = array();

		$projects = $this->get_all_projects();
		foreach($projects as $project)
		{
			$tickets = array_merge($tickets, $project->get_tickets());
		}

		return $tickets;
	}

	/**
	 * Retrieves all tickets associated with the codebase account specified by
	 * the credentials in the request object, only returns tickets that are of
	 * the specified status
	 *
	 * @param	string	$status_name
	 * @return	array	A collection of Codebase_Model_Ticket objects
	 */
	public function get_all_tickets_by_status($status_name)
	{
		$tickets = array();

		$projects = $this->get_all_projects();
		foreach($projects as $project)
		{
			$tickets = array_merge($tickets, $project->get_tickets_by_status($status_name));
		}

		return $tickets;
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
	 * Setter for the $request property (read only)
	 *
	 * @access	protected
	 * @param	Codebase_Request	$request	An instance of Codebase_Request
	 */
	protected function set_request(Codebase_Request $request) {
		$this->request = $request;
	}

}