<?php
/**
 * Extension of the Kohana HTTP_Cache class, used to provide a cache object
 * that is compatible with with the Kohana request object but ignores the
 * max-age cache header so that codebase API responses are always cached for the
 * amount of time specified in the options when the cache object is created.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 */
class Codebase_HTTP_Cache extends HTTP_Cache
{
	/**
	 * Override of the static factory method, used to provide an instance of
	 * this class
	 *
	 * @param	type				$cache
	 * @param	array				$options
	 * @return	Codebase_HTTP_Cache
	 */
	public static function factory($cache, array $options = array())
	{
		if ( ! $cache instanceof Cache)
		{
			$cache = Cache::instance($cache);
		}

		$options['cache'] = $cache;

		return new self($options);
	}

	/**
	 * Override of the set_cache method, works in exactly the same way except
	 * the check for the max-age header in the response has been removed so that
	 * Codebase API responses will always be cached, this breaks HTTP Cache
	 * rules but is the cleanest way of enabling caching for all responses
	 * within Kohana.
	 *
	 * @param	Response	$response
	 * @return	boolean
	 */
	public function set_cache(Response $response)
	{
		$headers = $response->headers()->getArrayCopy();

		if ($cache_control = Arr::get($headers, 'cache-control'))
		{
			// Parse the cache control
			$cache_control = HTTP_Header::parse_cache_control($cache_control);

			// If the no-cache or no-store directive is set, return
			if (array_intersect($cache_control, array('no-cache', 'no-store')))
				return FALSE;

			// Check for private cache and get out of here if invalid
			if ( ! $this->_allow_private_cache AND in_array('private', $cache_control))
			{
				if ( ! isset($cache_control['s-maxage']))
					return FALSE;

				// If there is a s-maxage directive we can use that
				$cache_control['max-age'] = $cache_control['s-maxage'];
			}

		}

		if ($expires = Arr::get($headers, 'expires') AND ! isset($cache_control['max-age']))
		{
			// Can't cache things that have expired already
			if (strtotime($expires) <= time())
				return FALSE;
		}

		return TRUE;
	}
}
