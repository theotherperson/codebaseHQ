<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Assignee_Core class, an instance of this class represents a
 * Codebase assignee/user, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * Codebase_Model_Assignee with the data returned from the API.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
class Codebase_Model_Assignee_Core extends Codebase_Model
{

	/**
	 * The properties of the assignee object as sepcified in the Codebase API
	 */
	protected $id = NULL;
	protected $company = NULL;
	protected $first_name = NULL;
	protected $last_name = NULL;
	protected $username = NULL;
	protected $email_address = NULL;


	/**
	 * static function to return all assignees belonging to the specified project
	 *
	 * @param	Codebase_Request	$request
	 * @param	string				$project_permalink
	 * @return	array				A collection Codebase_Model_Assignee objects
	 * @static
	 * @access	public
	 */
	public static function get_assignees_for_project(Codebase_Request $request, $project_permalink)
	{
		$path = '/'.$project_permalink.'/assignments';

		// TODO: Shouldn't have to specify the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
		return self::get_objects_for_path($request, 'Codebase_Model_Assignee', $path);
	}

	/**
	 * get the assignee's full name
	 *
	 * @return	string
	 */
	public function get_name()
	{
		return $this->get_first_name().' '.$this->get_last_name();
	}

}