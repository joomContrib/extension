<?php
/**
 * Abstract component extension
 *
 * @copyright  Copyright (C) 2014 joomContrib Team. All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see LICENSE.txt
 */

namespace joomContrib\Extension

/**
 * Component base class
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractComponent extends AbstractExtension
{
	/**
	 * {@inheritDoc}
	 */
	private $type = 'Component';

	/**
	 * Template path
	 *
	 * @param  string
	 */
	protected $temlatePath;

	/**
	 * Routes
	 *
	 * @param  array
	 */
	protected $routes;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get absolute template path
	 *
	 * @return  string
	 *
	 * @note    Check for template overwrites here?
	 */
	public function getTemplatePath()
	{
		if (!$this->templatePath)
		{
			$this->templatePath = $this->getPath() . '/templates';
		}

		return $this->templatePath;
	}

	/**
	 * Get routes
	 *
	 * @return  array
	 */
	public function getRoutes()
	{
		if (!$this->routes)
		{
			$this->routes = array();

			// Load from file
			$routesFile = $this->getPath() . '/config/routes.json';

			if (file_exists($routesFile))
			{
				$this->routes += json_decode(file_get_contents($routesFile));
			}

			// Load from with config
			$config = $this->getConfig();

			if ($config->exists('routes'))
			{
				$this->routes += (array) $config->get('routes');
			}
		}

		return $this->routes;
	}
}
