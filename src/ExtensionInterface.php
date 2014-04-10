<?php
/**
 * Extension Interface
 *
 * @copyright  Copyright (C) 2014 joomContrib Team. All rights reserved.
 * @license    GNU Lesser General Public License version 2 or later; see LICENSE.txt
 */

namespace joomContrib\Extension;

/**
 * Defines the interface for an Extension class.
 *
 * @since  __DEPLOY_VERSION__
 */
interface ExtensionInterface
{
	/**
	 * Get extension type
	 *
	 * @return  string
	 */
	public function getType();

	/**
	 * Get extension name.
	 * Consists of [vendor]/[packageName], just like in composer.json
	 *
	 * @return  string
	 */
	public function getName();

	/**
	 * Get the Extension namespace.
	 *
	 * @return  string
	 */
	public function getNamespace();

	/**
	 * Get the Extension directory path.
	 *
	 * @return  string  @return  string  The extension absolute path
	 */
	public function getPath();

	/**
	 * Get extension configuration
	 *
	 * @return  Joomla\Registry
	 */
	public function getConfig();
}
