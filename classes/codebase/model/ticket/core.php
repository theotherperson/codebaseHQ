<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Ticket_Core class, an instance of this class represents a
 * Codebase ticket, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * itself with the data returned from the API.
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
		$tickets = array();

		try
		{
			$response = $request->get('/'.$project_permalink.'/tickets');
			$response_data = self::parse_response($response);

			foreach($response_data as $ticket_data)
			{
				// TODO: Shouldn't have to reference the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
				$tickets[] = new Codebase_Model_Ticket($request, $ticket_data);
			}
		}
		catch(Codebase_Exception $e)
		{
			// something went wrong with the request, most likely there are no tickets associated with the project
			$tickets = array();
		}

		return $tickets;
	}

}