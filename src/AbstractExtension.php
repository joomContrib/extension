<?php
/**
 * Abstract Extension
 *
 * @copyright  Copyright (C) 2014 joomContrib Team. All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see LICENSE.txt
 */

namespace joomContrib\Extension;

use joomContrib\Extension\ExtensionInterface;

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
	 * Set extension data
	 *
	 * @param   array
	 *
	 * @return  $this
	 *
	 * @TODO  Reevaluate
	 */
	public function setData(array $data = array())
	{
		foreach ($data as $key => $node)
		{
			$this->key = $node;
		}

		return $this;
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

			// Set the last hump
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

			$this->name = implode('/', $vendorAndPackage);
		}

		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNamespace($sub = null)
	{
		$namespace = $this->reflected->getNamespaceName();

		return ($sub) ? $namespace . '\\' . $sub : $namespace;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPath($sub = null)
	{
		$path = dirname($this->reflected->getFileName());

		return ($sub) ? $path . '/' . $sub : $path;
	}

	/**
	 * Get Extension configuration
	 *
	 * @return  Joomla\Registry\Registry
	 *
	 * @todo  maybe nove part of ExtensionContainer so it may be accessed using DI.
	 *        and config read from json file.
	 */
	public function getConfig()
	{

		// LoadConfig
		if (!$this->config)
		{
			$this->config = new Registry;
		}

		return $this->config;
	}
}
