<?php
/**
 * @copyright  Copyright (C) 2014 joomContrib Team. All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see LICENSE.txt
 */

namespace joomContrib\Extension;

use Joomla\Event\Priority;
use Joomla\Event\EventInterface;

use Joomla\DI\Container;

/**
 * @note  class methods match event names, unless specified
 *  Subscribe plugin evens (Add listener to dispatcher)
 *
 * 	foreach ($this->extensions->plugins as $fqcn)
 * 	{
 *		$plugin = $this->container->buildObject($fqcn);
 * 
 *		$this->dispatcher->addListener($plugin);
 * 	}
 */
abstract class AbstractPluginExtension extends AbstractExtension
{
	/**
	 * {@inheritDoc}
	 */
	private $type = 'Plugin';

	/**
	 * Populate if you want to explicitly set event names or set priorities
	 * When empty, Dispatcher will use all public methods.
	 *
	 * @var  array
	 *
	 * @example:
	 *	'onBeforeExecute': Priority::NORMAL
	 *
	 * Events will receive EventInterface $event
	 *
	 * Consult Joomla\Event\Priority for example priorities
	 */
	private $events = array();

	/**
	 * Constructor.
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Get plugin events.
	 * Handy method for passing plugin as Listener,
	 *
	 * @return  array
	 *
	 * @see  Joomla\Event\Dispatcher::addListener
	 * note  Maybe remove it, Listener will get all the public methods
	 */
	public function getEvents()
	{
		// Get the '^on' methods
		if (empty($this->events))
		{
			$methods = $this->reflected->getMethods(ReflectionMethod::IS_PUBLIC);

			$this->events = array_filter($methods, function($method){
				return (substr($method, 0, 2) == 'on');
			});
		}

		return $this->events;
	}
}
