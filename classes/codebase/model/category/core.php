<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Category_Core class, an instance of this class represents a
 * Codebase category, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * Codebase_Model_Category with the data returned from the API.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
class Codebase_Model_Category_Core extends Codebase_Model
{

	/**
	 * The properties of the category object as sepcified in the Codebase API
	 */
	protected $id = NULL;
	protected $name = NULL;

	/**
	 * static function to return all categories belonging to the specified project
	 *
	 * @param	Codebase_Request	$request
	 * @param	string				$project_permalink
	 * @return	array				A collection Codebase_Model_Category objects
	 * @static
	 * @access	public
	 */
	public static function get_categories_for_project(Codebase_Request $request, $project_permalink)
	{
		$path = '/'.$project_permalink.'/tickets/categories';

		// TODO: Shouldn't have to specify the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
		return self::get_objects_for_path($request, 'Codebase_Model_Category', $path);
	}

}