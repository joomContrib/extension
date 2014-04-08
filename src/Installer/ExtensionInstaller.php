<?php
/**
 * Extension installer ivoked by composer
 *
 * @see  Composer:Plugins            https://getcomposer.org/doc/articles/plugins.md
 * @see  Composer:Custom installers  https://getcomposer.org/doc/articles/custom-installers.md
 * @see  joomla-disto                https://github.com/joomla-distro/cms-distro-core/blob/master/src/BaseInstaller.php
 * @see  Composer\Installer          https://github.com/composer/composer/tree/master/src/Composer/Installer
 */

namespace joomContrib\Extension\Instaler;

use Composer\Compser;
use Composer\IO\Interface;
use Composer\Package\InstalledRepositoryInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Installer\LibraryInstaller;

use Joomla\Event\Event;
use Joomla\Event\Dispatcher;

class Installer extends LibraryInstaller
{
	/**
     * String with location path
     */
    protected $location;

    /**
     * String with type
     */
    protected $support = '';

	/**
     * Composer Config
	 *
	 * @var  array
     */
    protected $config = array();

	/**
	 * {@inheritDoc}
	 */
	public function __construct(IOInterface $io, Composer $composer, $config = array(), $type = 'library')
	{
		parent::__construct($io, $composer, $type);

		$this->config = $composer->getConfig();

		if (!empty($config['type']) && empty($this->support))
		{
			$this->support = $config['type'];
		}

		if (!empty($config['location']) && is_null($this->location))
		{
			$this->location = $config['location'];
		}

		$this->dispatcher = new Dispatcher;
	}

	/**
     * Set the dispatcher to use.
     *
     * @param   DispatcherInterface  $dispatcher  The dispatcher to use.
     *
     * @return  $this  This method is chainable.
     */
	public function setDispatcher(DispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInstallPath(PackageInterface $package)
	{
		return $this->getLocation($package);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Similar on Update and on Uninstall
	 */
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
		// @event onBeforeInstall
		$this->triggerEvent((new Event('onBeforeInstall'))
			->setArgument('InstalledRepository', $repo)
			->setArgument('Package', $package));

		parent::install($repo, $package);

		// @event onAfterInstall
		$this->triggerEvent((new Event('onAfterInstall'))
			->setArgument('InstalledRepository', $repo)
			->setArgument('Package', $package));

		return;
	}

	/**
	 * @return  string  Path
	 */
	protected function getLocation(PackageInterface $package)
	{
		
	}
}
