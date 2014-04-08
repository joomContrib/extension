Extension package
=================

Unlike Joomla CMS, extensions are not resolved by location in filesystem, but by namespace.

- How extensions communicate between each other? There must be global registry.
- Installation scripts


## AbstractExtension

Abstract extension class. Use it for custom extension types.


_Available Methods_

- `getType`: Get extension type
- `getName`: Get extension name
- `getNamespace`: Get namespace
- `getPath`: Get absolute directory path in filesystem
- `getConfig`: Load extension configuration from database or json file.


### ComponentExtension

_Available methods_

- `getTemplatePath` Template path, relative to extension directory

*Usage*

`Extension\[vendor]\FooComponent\FooComponent.php`

```PHP
namespace Extension\[vendor]\FooComponent;

use joomContrib\Extension\AbstractComponentExtension;

class FooComponent extends AbstractComponentExtension {}
```

`Extension\[vendor]\FooComponent\Controller\BarComponent.php`

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
		$extension = $this->extensionManager->getExtension($this);
		$extension = $this->extensionManager->getExtension('microsoft\windowsComponent');

		// Get template file
		$template = $extension->getTemplatePath() . '/' . 'bar.php';

		// Return rendered template file
		return include_once $template;
	}
}

```


### PluginExtension



## ExtensionInstaller

This is hooked up to Composers' [Library Installer](https://github.com/composer/composer/blob/master/src/Composer/Installer/LibraryInstaller.php).
Provides installation/ update/ uninstallation methods, and triggers adequate events (ie. `onBeforeInstall` and `onAfterInstall`).


## ExtensionManager

Container of all registered extensions


## ExtensionManager Service Provider

Use it in your application.

```PHP
use Joomla\DI\Container;
use joomContrib\Extension\ExtensionManagerServiceProvider;

$container = new Container;

$container->registerServiceProvider(new ExtensionManagerServiceProvider(ExtensionManagerServiceProvider::DATABASE));


```



Application runtime:

1) Load extensions data (db/ file)
2) Locate extension by routes
3) 

Maps extension names to extension namespaces.
