<?php
/**
 * @name       ExtensionServiceProvider
 * @package    joomContrib\Extension
 * @copyright  Copyright (C) 2014 joomContrib Team (https://github.com/orgs/joomContrib). All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see https://www.gnu.org/licenses/lgpl.html
 */

namespace joomContrib\Extension;

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
	protected $options = array(
	//	'sourceFile' => 'extensions.json',
	//	'sourceTable' => '#__extensions',
		'aliasPrefix' => 'e/',
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
	 * @param   array  $options  Service Configuration
	 */
	public function __construct(array $options = array())
	{
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * {@inheritDoc}
	 */
	public function register(Container $container)
	{
		$this->container = $container;

		$aliasPrefix = $this->options['aliasPrefix'];

		// Process each extension
		foreach ($this->loadData() as $alias => $entry)
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
					// Build an object
					$className = $entry->fqcn;
					$instance = new $className($c);
					
					// Note: may be false if cannot resolve FQCN
					// Fatal error: Maximum function nesting level of '100' reached, aborting!
				//	$instance = $c->buildObject($entry->fqcn, true);

					// Set data (config, routes or anything else)
					$instance->setData((array) $entry);


					return $instance;
				},
				true
			);


			$container->alias($aliasPrefix . $alias, $entry->fqcn);
		}


		// Provide access to own methods
		$className = get_class($this);

		$container->share($className, $this, true);
		$container->alias($aliasPrefix . 'serviceProvider', $className);


		return;
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
			$results += (array) json_decode(file_get_contents($this->options['sourceFile']));
		}


		// Load from database if needed
		if (isset($this->options['sourceTable'])
			&& $this->container->exists('Joomla\\Database\\DatabaseDriver')
		)
		{
			$db = $this->container->get('Joomla\\Database\\DatabaseDriver');

			$query = 'SELECT * FROM #__extensions';
			$db->setQuery($query);

			/* @throws \RuntimeException */
			$results += $db->loadObjectList('alias');
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
		$fqcn = get_class($extension);

		// Share object
		$this->container->share(
			$fqcn,
			$extension,
			true
		);

		// Set alias
		$this->container->alias($this->options['aliasPrefix'] . $extension->getName(), $fqcn);

		return $this;
	}

	/**
	 * Find extension by key, without using alias prefix
	 *
	 * @param   $key
	 *
	 * @return  instaceof ExtensionInterface
	 */
	public function findOneByAlias($key)
	{
		return $this->container->get($this->options['aliasPrefix'] . $key);
	}

	/**
	 * Get list of extensions matching criteria
	 *
	 * @param   array    $criteria  Criteria to match, ie, array('type' => 'Plugin')
	 *                              Available options are: type, namespace, fqcn
	 * @param   boolean  $instance  Get FQCNs or instances
	 *
	 * @return  array
	 */
	public function findBy(array $criteria = array(), $asInstance = true)
	{
		$results = array();

		// Looup
		foreach ($this->dataStore as $fqcn => $node)
		{
			$intersection = array_intersect_assoc((array) $node, $criteria);

			if (!empty($intersection))
			{
				$results[] = ($asInstance) ? $this->container->get($fqcn) : $fqcn;
			}
		}

		return $results;
	}

	/**
	 * Get Extension by controller
	 *
	 * @param   instanceof ControllerInterface  $controller
	 *
	 * @return  instanceof Extension
	 *
	 * @thorws  InvalidArgumentException  Key has not been registered with the container
	 */
	public function findOneByController(ControllerInterface $controller)
	{
		// Extract extension namespace
		list($namespace, $controllerPath) = explode('\\Controller\\', get_class($controller));

		// Build FQCN
		$fqcn = '\\' . $namespace . '\\' . basename($namespace);

		return $this->container->get($fqcn);
	}
}
