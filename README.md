Extension package __Work in Progress__
=================

Unlike Joomla CMS, extensions are not resolved by location in filesystem, but by namespace.

- How extensions communicate between each other? There must be global registry.
- Installation scripts


## AbstractExtension

Abstract extension class. Use it for custom extension types.


**Available Methods**

- `getType`: Get extension type
- `getName`: Get extension name
- `getNamespace`: Get namespace
- `getPath`: Get absolute directory path in filesystem
- `getConfig`: Load extension configuration from database or json file.


### AbstractComponent

**Available methods**

- `getTemplatePath` Template path, relative to extension directory
- `getRoutes` Load component routes

**Usage**

Component code (`Extension\[vendor]\FooComponent\FooComponent.php`):

```PHP
namespace Extension\[vendor]\FooComponent;

use joomContrib\Extension\AbstractComponent;

class FooComponent extends AbstractComponent {}
```

Controller code (`Extension\[vendor]\FooComponent\Controller\BarComponent.php`):

```PHP
namespace Extension\[vendor]\FooComponent\Controller;

use joomContrib\Extension\ExtensionManager;

use Joomla\Controller\AbstractController;
use Joomla\Input\Input;
use Joomla\Application;

class BarController extends AbstractController
{
	public function __construct(Input $input = null, AbstractApplication $app = null, ExtensionManager $extensionManager = null)
	{
		parent::__construct($input, $app);

		$this->extensionManager = $extensionManager;
	}

	public function execute()
	{
		// Get parent extension
		$extension = $this->extensionManager->getExtensionFor($this);
		$extension = $this->extensionManager->getExtension('vendor\fooComponent');

		// Get template file
		$template = $extension->getTemplatePath() . '/' . 'bar.html.php';

		// Return rendered template file
		return include_once $template;
	}
}
```


### AbstractPlugin

**Available methods**

- `getEvents`: Get available events, optinally may define priorities


## ExtensionInstaller

@TODO

This is hooked up to Composers' [Library Installer](https://github.com/composer/composer/blob/master/src/Composer/Installer/LibraryInstaller.php).
Provides installation/ update/ uninstallation methods, and triggers adequate events (ie. `onBeforeInstall` and `onAfterInstall`).


## ExtensionManager

@TODO

Container of all registered extensions


## ExtensionManager Service Provider

@TODO

Use it in your application.

```PHP
use Joomla\DI\Container;
use joomContrib\Extension\ExtensionManagerServiceProvider;

$container = new Container;

$container->registerServiceProvider(new ExtensionManagerServiceProvider(ExtensionManagerServiceProvider::DATABASE));

```