<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Project_Core class, an instance of this class represents a
 * Codebase project, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * itself with the data returned from the API.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
class Codebase_Model_Project_Core extends Codebase_Model
{

	/**
	 * The properties of the project object as sepcified in the Codebase API
	 */
	protected $group_id = NULL;
	protected $icon = NULL;
	protected $name = NULL;
	protected $overview = NULL;
	protected $permalink = NULL;
	protected $start_page = NULL;
	protected $status = NULL;

	/**
	 * Holds the tickets belonging to this project
	 *
	 * @var array
	 */
	protected $tickets = NULL;

	/**
	 * static function to return all projects belonging to the Codebase account
	 * as specified in the request object.
	 *
	 * @param	Codebase_Request	$request
	 * @return	array				A collection Codebase_Model_Project objects
	 * @static
	 * @access	public
	 */
	public static function get_all_projects(Codebase_Request $request)
	{
		$projects = array();

		$response = $request->get('/projects');
		$response_data = self::parse_response($response);

		foreach($response_data as $project_data)
		{
			// TODO: Shouldn't have to reference the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
			$projects[] = new Codebase_Model_Project($request, $project_data);
		}

		return $projects;
	}

	/**
	 * Returns all tickets belonging to the project
	 *
	 * @return	array	A collection Codebase_Model_Ticket objects
	 */
	public function get_tickets()
	{
		if($this->tickets === NULL)
		{
			$this->tickets = Codebase_Model_Ticket::get_tickets_for_project($this->get_request(), $this->get_permalink());
		}

		return $this->tickets;
	}

}