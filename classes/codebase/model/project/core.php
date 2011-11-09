<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Project_Core class, an instance of this class represents a
 * Codebase project, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * Codebase_Model_Project with the data returned from the API.
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
	 * The tickets belonging to this project
	 *
	 * @var array
	 */
	protected $tickets = NULL;

	/**
	 * The statuses belonging to this project
	 *
	 * @var array
	 */
	protected $statuses = NULL;

	/**
	 * The milestones belonging to this project
	 *
	 * @var array
	 */
	protected $milestones = NULL;

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
		$path = '/projects';

		// TODO: Shouldn't have to specify the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
		return self::get_objects_for_path($request, 'Codebase_Model_Project', $path);
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

	/**
	 * Returns all statuses belonging to the project
	 *
	 * @return	array	A collection Codebase_Model_Status objects
	 */
	public function get_statuses()
	{
		if($this->statuses === NULL)
		{
			$this->statuses = Codebase_Model_Status::get_statuses_for_project($this->get_request(), $this->get_permalink());
		}

		return $this->statuses;
	}

	/**
	 * Returns all milestones belonging to the project
	 *
	 * @return	array	A collection Codebase_Model_Milestone objects
	 */
	public function get_milestones()
	{
		if($this->milestones === NULL)
		{
			$this->milestones = Codebase_Model_Milestone::get_milestones_for_project($this->get_request(), $this->get_permalink());
		}

		return $this->milestones;
	}

}