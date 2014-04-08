<?php
/**
 * Extension Manager
 */

namespace joomContrib\Extension;

/**
 */
class ExtensionManager
{
	const SOURCE_FILE = '1';
	const SOURCE_DB = '2';

	protected $loaded;

	/**
	 * Collection of loaded extensions.
	 */
	protected $registry = array();

	/**
	 */
	public function __construct()
	{
	}

	protected function load()
	{
		if ($source_type == SOURCE_DB)
		{
			$db = $this->container->get('Joomla\\Database\\DatabaseDriver');
			$query = 'SELECT * FROM #__extensions';

			$this->registry = $db->loadObjectList('key');
		}
		else if ($source_type == SOURCE_FILE)
		{
			$this->registry = (new \Joomla\Registry\Registry($this->file))->asArray();
		}

		return;
	}

	/**
	 * Get extension by name
	 */
	public function getExtension($key)
	{
		if (!$this->loaded)
		{
			$this->load();
		}

		$registry = $this->registry;
	}
}
