<?php
/**
 * Abstract Extension
 *
 * @copyright  Copyright (C) 2014 joomContrib Team. All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see LICENSE.txt
 */

namespace joomContrib\Extension;

use joomContrib\ExtensionInterface;

use Joomla\DI\Container;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Registry\Registry;

/**
 * Extension base class
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractExtension implements ExtensionInterface
{
	use ContainerAwareTrait;

	/**
	 * Extension type
	 *
	 * @var  string  Component|Plugin|Module|Theme|Media
	 */
	private $type;

	/**
	 * Reflected class
	 *
	 * @var    \ReflectionClass
	 */
	protected $reflected;

	/**
	 * Extension name (vendor/package)
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * Extension configuration
	 *
	 * @var  Joomla\Registry\Registry
	 */
	protected $config;

	/**
	 * Constructor.
	 */
	public function __construct(Container $container)
	{
		$this->reflected = new \ReflectionClass($this);
		$this->setContainer($container);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType()
	{
		if (!$this->type)
		{
			// Split on camel case
			$camel = preg_split('/(?=[A-Z])/', get_class($this), -1, PREG_SPLIT_NO_EMPTY);

			// Set the last part
			$this->type = array_pop($camel);
		}
	
		return $type;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		// Read two last parts
		if (empty($this->name))
		{
			$classArray = explode('\\', $this->getNamespace());
			$vendorAndPackage = array_slice($classArray, -2);

			$this->name = implode('\\', $vendorAndPackage);
		}

		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNamespace()
	{
		return $this->reflected->getNamespaceName();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPath()
	{
		return dirname($this->reflected->getFileName());
	}

	/**
	 * Get Extension configuration
	 *
	 * @return  Joomla\Registry\Registry
	 *
	 * @todo  maybe nove part of ExtensionManager so it may be accessed using DI.
	 *        and config read from json file.
	 */
	public function getConfig()
	{
		return new Registry;

		// LoadConfig
		if (!$this->config)
		{
			$db = $this->getContainer()->get('Joomla\\Database\\DatabaseDriver');

			$query = substr('SELECT e.* FROM #__extensions WHERE e.type = %s AND e.name = %s', $this->getType(), $this->getName());
			$db->setQuery($query):

			$extensionData = $db->loadObject();

			// Load up non-scalar or json string
			$this->config = new Registry($extensionData->config);
		}

		return $this->config;
	}
}
