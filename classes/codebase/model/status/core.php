<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Status_Core class, an instance of this class represents a
 * Codebase status, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * Codebase_Model_Status with the data returned from the API.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
class Codebase_Model_Status_Core extends Codebase_Model
{

	/**
	 * The properties of the status object as sepcified in the Codebase API
	 */
	protected $id = NULL;
	protected $name = NULL;
	protected $background_colour = NULL;
	protected $order = NULL;
	protected $treat_as_closed = NULL;
	protected $colour = NULL;

	/**
	 * static function to return all statuses belonging to the specified project
	 *
	 * @param	Codebase_Request	$request
	 * @param	string				$project_permalink
	 * @return	array				A collection Codebase_Model_Status objects
	 * @static
	 * @access	public
	 */
	public static function get_statuses_for_project(Codebase_Request $request, $project_permalink)
	{
		$path = '/'.$project_permalink.'/tickets/statuses';

		// TODO: Shouldn't have to specify the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
		return self::get_objects_for_path($request, 'Codebase_Model_Status', $path);
	}

	/**
	 * setter for boolean treat_as_closed property, overriden here so that the
	 * value can be converted to a boolean
	 *
	 * @param	mixed	$treat_as_closed
	 */
	public function set_treat_as_closed($treat_as_closed)
	{
		$value = FALSE;

		if($treat_as_closed == 'true' OR $treat_as_closed === TRUE OR $treat_as_closed === 1)
		{
			$value = TRUE;
		}

		$this->treat_as_closed = $value;
	}

}