<?php
/**
 * @name       ExtensionServiceProvider
 * @package    joomContrib\Extension
 * @copyright  Copyright (C) 2014 joomContrib Team (https://github.com/orgs/joomContrib). All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see https://www.gnu.org/licenses/lgpl.html
 */

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;
use Joomla\Controller\ControllerInterface;

/**
 * Registers extensions for application.
 *
 * @since  __DEPLOY_VERSION__
 */
class ExtensionServiceProvider implements ServiceProviderInterface
{
	/**
	 * Default configuration
	 *
	 * @var  array
	 */
	protected $config = array(
		'sourceFile' => 'extensions.json',
		'sourceTable' => '#__extensions',
		'aliasPrefix' => 'e/'
	);

	/**
	 * Container object
	 *
	 * @var  Container
	 */
	protected $container;

	/**
	 * Registry of all extensions
	 *
	 * @var  array
	 */
	protected $dataStore = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Service Configuration
	 */
	public function __construct(array $config = array())
	{
		$this->config = array_merge($this->config, $config);
	}

	/**
	 * {@inheritDoc}
	 */
	public function register(Container $container)
	{
		$this->container = $container;

		$aliasPrefix = $this->options['aliasPrefix'];

		// Process each extension
		foreach ($this->loadData($c) as $entry)
		{
			$shortName = basename($entry->namespace);

			// Get FQCN
			if (!isset($entry->fqcn))
			{
				$entry->fqcn = $entry->namespace . '\\' . $shortName;
			}

			// Auto set type by last hump
			if (!isset($entry->type))
			{
				$camelSplit = preg_split('~(?=[A-Z])~', $shortName, -1, PREG_SPLIT_NO_EMPTY);
				$entry->type = array_pop($camelSplit);
			}


			// Save in internal stoage
			$this->dataStore[$entry->fqcn] = $entry;


			// Share extension
			$container->share(
				$entry->fqcn,
				function (Container $c) use ($entry)
				{
					static $instance;

					// Instantiate
					if (!$instance)
					{
						$instance = $c->buildObject($entr->fqcn, true);

						// Set data (config, routes or anything else)
						$instance->setData($entry);
					}

					return $instance;
				},
				true
			);

			$container->alias($aliasPrefix . $entry->alias, $fqcn);
		}


		// Provide access to own methods
		$className = get_class($this);

		$container->share($className, $this, true);
		$container->alias($aliasPrefix . 'serviceProvider', $className);


		return;
	}

	/**
	 * Get list of extensions filtered by single criteria
	 *
	 * @param   string   $by      Criteria
	 * @param   boolean  $asKeys  Get keys or instances
	 *
	 * @return  array
	 */
	public function filter($by = 'type', $value, $asKeys = false)
	{
		$results = array();

		// Looup
		foreach ($this->dataStore as $node)
		{
			if (isset($node->{$by} && $node->{$by} == $value)
			{
				$results[] = ($asKeys)
					? $node->fqcn
					: $this->container->get($fqcn);
			}
		}


		return $results;
	}

	/**
	 * Set extenion in container
	 *
	 * @param   instaceof ExtensionInterface  $extension
	 *
	 * @return  $this
	 */
	public function add(ExtensionInterface $extension)
	{
		$fqcn = get_class($extension)

		// Share object
		$this->container->share(
			get_class($extension),
			$extension,
			true
		);

		// Set alias
		$this->container->alias($extension->getName(), $extension);

		return $this;
	}

	/**
	 * Get Extension by controller
	 *
	 * @param   instanceof ControllerInterface  $controller
	 *
	 * @return  instanceof Extension
	 */
	public function byController(ControllerInterface $controller)
	{
		// Lookup namespaces
		$namespaceAndController = explode('\\Controller\\', get_class($controller));

		// Use two last parts of a namespace
		$nsArray = explode('\\', $namespaceAndController);

		$namespace = implode('\\', array_slice($nsArray, -2));
		$fqcn = $namespace . '\\' . basename($namespace);


		return $this->container->get($fqcn);
	}

	/**
	 * Load data
	 *
	 * @param   Container  $container
	 *
	 * @return  array
	 */
	protected function loadData()
	{
		$results = array();

		// Load from file if available
		if (isset($this->options['sourceFile']) 
			&& is_readable($this->options['sourceFile'])
		)
		{
			// Load results
			$results += (new Registry($this->options['sourceFile'])->asArray();
		}


		// Load from database if needed
		if (isset($this->options['sourceTable']))
		{
			$db = $this->container->get('Joomla\\Database\\DatabaseDriver');

			$query = 'SELECT * FROM #__extensions';
			$db->setQuery($query);

			/* @throws \RuntimeException */
			$results += $db->loadObjectList('alias');
		}


		return $results;
	}
}
