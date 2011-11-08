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

	protected $group_id;
	protected $icon;
	protected $name;
	protected $overview;
	protected $permalink;
	protected $start_page;
	protected $status;

	public static function get_all_projects(Codebase_Request $request)
	{
		$projects = array();

		$response = $request->get('/projects', array());
		$response_data = self::parse_response($response);

		foreach($response_data as $project_data)
		{
			// TODO: Shouldn't have to reference the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
			$projects[] = new Codebase_Model_Project($request, $project_data);
		}

		return $projects;
	}

}