<?php defined('SYSPATH') or die('No direct script access.');
/**
 * NemoAPI_User - Handles all requests/responses to/from the Nemo API that are
 * related to users including authentication.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 */
class NemoAPI_User extends NemoAPI
{
	/**
	 * The Nemo API path to the log in action
	 *
	 * @const	string	PATH_LOG_IN
	 */
	const PATH_LOG_IN = '/login';

	/**
	 * The Nemo API path to the check session action
	 *
	 * @const	string	PATH_CHECK_SESSION
	 */
	const PATH_CHECK_SESSION = '/checksession';

	/**
	 * Nemo's UID for the user
	 *
	 * @var		int			$id
	 * @access	protected
	 */
	protected $id = NULL;

	/**
	 * Nemo's session ID for this user session
	 *
	 * @var		int			$session_id
	 * @access	protected
	 */
	protected $session_id = NULL;

	/**
	 * ID of the franchise the user belongs to
	 *
	 * @var		int			$franchise_id
	 * @access	protected
	 */
	protected $franchise_id = NULL;

	/**
	 * User's full name
	 *
	 * @var		string		$name
	 * @access	protected
	 */
	protected $name = NULL;

	/**
	 * The Nemo username of the user
	 *
	 * @var		string		$username
	 * @access	protected
	 */
	protected $username = NULL;

	/**
	 * The user's email address
	 *
	 * @var		string		$email_address
	 * @access	protected
	 */
	protected $email_address = NULL;

	/**
	 * Attempts to log in to Nemo via the Nemo API with the supplied username
	 * and password, returns an instance of NemoAPI_Model_User on success or
	 * NULL if response is not successful
	 *
	 * @param	string			$username	The Nemo username to log in with
	 * @param	string			$password	The Nemo password to log in with
	 * @return	NemoAPI_User				If logged in successfully an instance of NemoAPI_User will be returned containing the user's data from Nemo
	 * @access	public
	 * @static
	 */
	public static function log_in($username, $password)
	{
		$user = NULL;

		// build the parmeters for the request
		$params = array(
			'username' => $username,
			'password' => $password
		);

		// make the request
		$response_data = self::post(self::PATH_LOG_IN, $params);

		// if the response was successful instantiate a new user object with the data from the response
		if($response_data['status'] == self::STATUS_SUCCESS)
		{
			// create a new NemoAPI_User object
			$user = new self($response_data['user']);
		}

		// return the user object
		return $user;
	}

    /**
	 * Makes a call to the Nemo API to check if the provided session_id is a
	 * valid, authenticated session
	 *
	 * @param	string			$session_id		The Nemo session ID to check
	 * @return	NemoAPI_User					If the session is ok, an instance of NemoAPI_User will be returned containing the user's data from Nemo
	 * @access	public
	 * @static
	 */
	public static function check_session($session_id)
	{
		$user = NULL;

		// build the parmeters for the request
		$params = array(
			'session_id' => $session_id
		);

		// make the request
		$response_data = self::post(self::PATH_CHECK_SESSION, $params);

		// if the response was successful instantiate a new user object with the data from the response
		if($response_data['status'] == self::STATUS_SUCCESS)
		{
			// create a new NemoAPI_User object
			$user = new self($response_data['user']);
		}

		// return the user object
		return $user;
	}
}