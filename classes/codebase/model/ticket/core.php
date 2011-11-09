<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Ticket_Core class, an instance of this class represents a
 * Codebase ticket, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * Codebase_Model_Ticket with the data returned from the API.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
class Codebase_Model_Ticket_Core extends Codebase_Model
{

	/**
	 * The properties of the ticket object as sepcified in the Codebase API
	 */
	protected $ticket_id = NULL;
	protected $summary = NULL;
	protected $ticket_type = NULL;
	protected $reporter_id = NULL;
	protected $assignee_id = NULL;
	protected $assignee = NULL;
	protected $reporter = NULL;
	protected $category = NULL;
	protected $priority = NULL;
	protected $status = NULL;
	protected $milestone = NULL;
	protected $deadline = NULL;
	protected $updated_at = NULL;
	protected $estimated_time = NULL;
	protected $category_id = NULL;
	protected $priority_id = NULL;
	protected $status_id = NULL;
	protected $milestone_id = NULL;

	/**
	 * The project that this ticket belongs to
	 *
	 * @var		Codebase_Model_Project
	 */
	protected $project = NULL;

	/**
	 * static function to return all tickets belonging to the specified project
	 *
	 * @param	Codebase_Request	$request
	 * @param	string				$project_permalink
	 * @return	array				A collection Codebase_Model_Ticket objects
	 * @static
	 * @access	public
	 */
	public static function get_tickets_for_project(Codebase_Request $request, $project_permalink)
	{
		$path = '/'.$project_permalink.'/tickets';

		// TODO: Shouldn't have to specify the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
		return self::get_objects_for_path($request, 'Codebase_Model_Ticket', $path);
	}

	/**
	 * getter for the project property
	 *
	 * @return	Codebase_Model_Project
	 */
	public function get_project() {
		return $this->project;
	}

	/**
	 * setter for the project property
	 *
	 * @param	Codebase_Model_Project	$project
	 */
	public function set_project(Codebase_Model_Project $project) {
		$this->project = $project;
	}

	/**
	 * getter for the status property
	 *
	 * @return	Codebase_Model_Status
	 */
	public function get_status() {
		if(!$this->status instanceOf Codebase_Model_Status)
		{
			$statuses = $this->get_project()->get_statuses();
			foreach($statuses as $status)
			{
				if($status->get_id() == $this->get_status_id())
				{
					$this->set_status($status);
				}
			}
		}

		return $this->status;
	}

	/**
	 * getter for the assignee property
	 *
	 * @return	Codebase_Model_Assignee
	 */
	public function get_assignee() {
		if(!$this->assignee instanceOf Codebase_Model_Assignee)
		{
			$assignees = $this->get_project()->get_assignees();
			foreach($assignees as $assignee)
			{
				if($assignee->get_id() == $this->get_assignee_id())
				{
					$this->set_assignee($assignee);
				}
			}
		}

		return $this->assignee;
	}

}