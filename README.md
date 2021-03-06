Extension package
=================

_Work in Progress_

What is Extension here? It's a package specific to the Application, so quite similar to Joomla CMS Extensions, Symfony2 Bundles or Wordpress Plugins.

This packages is trying to assists in solving issues:

- Moving some resources from Application to Extensions (like routes)
- Provide helpers for extensions to communicate between each other
- Extension Installation (todo)



## AbstractExtension

Abstract extension class. Use it for developing custom extension types.

### Usage


#### The `getType` method

Retrieve extension type


#### The `getName` method

Retrieve extension name


#### The `getNamespace` method

Retrieve extension namespace


#### The `getClass` method


Retrieve extension class

**Accepted Parameters**

- `$nested`: Nested namespace

Example: `$profileModel = $extension->getClass('Model\\Profile');`


#### The `getPath` method

Get absolute directory path in the filesystem

**Accepted Parameters**

- `$Nub`: Nested path

Example: `$template = $extension->getPath('profile/view.html.twig');`


#### The `getConfig` method

Retrieve configuration


#### Registering extension service provider

Register extension service provider, located in `Providers` subdirectory.

```php
use joomContrib\Extension\AbstractPlugin;
use Joomla\DI\Container;

class MyPlugin extends AbstrctPlugin
{
	public function __construct(Container $container)
	{
		parent::__construct($container);

		$container->registerServiceProvider($this->getClass('Providers\MyServiceProvider'));
	}
}
```


#### Composer setup

```JSON
{
	"autoload": {
		"psr-4" : {
			"Extension\\"			: "Extension//"
		}
	}
}
```


## AbstractComponent

Base class for component extension

**Component code** (_Extension/[vendor]/FooComponent/FooComponent.php_):

```PHP
namespace Extension\[vendor]\FooComponent;

use joomContrib\Extension\AbstractComponent;

class FooComponent extends AbstractComponent {}
```

**Controller code** (_Extension/[vendor]/FooComponent/Controller/BarComponent.php_):

```PHP
namespace Extension\[vendor]\FooComponent\Controller;

use joomContrib\Extension\ExtensionContainer;

use Joomla\Controller\AbstractController;
use Joomla\DI\ContainerAwareTrait;

class BarController extends AbstractController implements 
{
	use ContainerAwareTrait;

	public function execute()
	{
		// Get foreign extension by alias
		$fooComponent = $this->getContainer()->get('e/vendor/FooComponent');

		// Get it's template file
		$template = $fooComponent->getTemplatePath() . '/bar.html.php';

		// Return rendered template file
		return include_once $template;
	}
}
```


### Usage

#### The `getTemplatePath` method

Get absolute template path (relative to extension directory)

#### The `getRoutes` method

Get component routes


#### Suggested file structure

**Component folder** _Extensions/vendor/FooComponent/_

```
Controller/
    Item/
	    ViewController.php
        EditController.php
    ViewController.php
Entity/  <-- When using Doctrine/ORM
     Item.php
Model/  <-- When using Joomla/Model
    ItemModel.php
Providers/ <-- For own Service Providers
	MyServiceProvider.php
View/  <-- When using Joomla/View
    ItemHtmlView.php
templates/
    item/
        view.html.twig
        edit.html.twig
	default/
		view.xml.twig
    layout.html.twig  <-- Only when component renders whole page
config/
    doctrine/  <-- When using Doctrine/ORM
        Sub.orm.yml
    config.json
    routes.json
FooComponent.php  <-- Extension
```



## AbstractPlugin

### Usage

Plugin registration during application init
```PHP

$dispatcher = $this->getDispatcher();
$extensionServiceProvider = $this->getContainer()->get('e/serviceProvider');

// Get all registered plugins
$plugins = $extensionServiceProvider->findBy(array('type' => 'Plugin'));

// Register each plugin as event listener in dispatcher
foreach ($plugins as $plugin)
{
	$dispatcher->addListerner($plugin, $plugin->getEvents());
}

```

#### The `getEvents` method

Get available events, optionally with defined priorities

#### Suggested file structure

**Plugin folder** _Extensions/vendor/FooPlugin/_

```
FooPlugin.php  <-- Extension
```

**Plugin code**

```PHP
namespace Extension\vendor\FooPlugin;

use joomContrib\Extension\AbstractPlugin;
use Joomla\Event\Priority;

class FooPlugin extends AbstractPlugin
{
	// Populate if want to specify priorities
	private $events = array(
		'onAfterExecute': Priority::LOW
	);

	public function onAfterExecute($event)
	{
		echo '!Hola Mundo!';
	}
}
```


## Extension Service Provider

Thanks to this fellow

- you are able to access instance of every extension by defined alias. Instances are built once and only on demand.

### Usage

**Register**

```PHP
use Joomla\DI\Container;
use joomContrib\Extension\ExtensionServiceProvider;

$container = new Container;

$container->registerServiceProvider(
	new ExtensionServiceProvider(array(
		'sourceFile' => APPLICATION_ROOT . '/app/config/extensons.json')));
```

#### The `__construct` method

**Accepted Parameters**

- `$options`:
  - `sourceFile`: Location of .json file (ie. `$app_root . '/etc/extensions.json`).
  - `sourceTable`: Name of database table (ie. `#__extensions`). The instance of _Joomla\Database\DatabaseDriver_ should be available in container.
  - `aliasPrefix`: Container aliases prefix to `e/`.

#### The `add` method

**Accepted parameters**

- `$extension`: Extension instance to add

#### The `findBy` method

Lookup extensions matching criteria

**Accepted Parameters**

- `$criteria`: Criteria to match, ie, `array('type' => 'Plugin')`
- `$asInscance`: Return instances (default) or FQCNs

**Example**

```PHP
$extensionServiceProvider = $container->get('e/serviceProvider');

$plugins = $extensionServiceProvider->findBy(array('type' => 'Plugin'));
```

#### The `findOneByController` method

Return extension of controller

**Accepted Parameters**

- `$controller`: Controller to resolve

**Example**

```PHP
class FooController
{
	public function execute()
	{
		$extensionServiceProvider = $this->getContainer()->get('e/serviceProvider');
		$thisExtension = $extensionServiceProvider->findOneByController($this);
	}
}
```


## ExtensionInstaller

@TODO

Hooked up to Composers' [Library Installer](https://github.com/composer/composer/blob/master/src/Composer/Installer/LibraryInstaller.php).
Provides installation/ update/ uninstallation methods, and triggers adequate events (ie. `onBeforeInstall` and `onAfterInstall`).



Disclamer
---------

Concept base on

- [Symfony2](https://github.com/symfony/symfony/) (Bundles)
- [Joomla CMS](https://github.com/joomla/joomla-cms/) (Extensions)
- [Joomla-Distro proposal](https://github.com/joomla-distro/) by [J�lio Pontes](https://github.com/juliopontes) (Installation)


Resources
---------
- [composer/installers](https://github.com/composer/installers): Temporary solution to install extensions (as Joomla CMS ext.) in custom directory:

```json
{
	"extra": {
		"installer-paths": {
			"Extensions/{$name}/": ["type:joomla-component", "type:joomla-plugin"]
		}
	}
}
```

- [CackePHP packages](http://plugins.cakephp.org/packages/) - Same concept
- [Packagyst](http://packalyst.com/)