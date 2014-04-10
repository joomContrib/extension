Extension package
=================

_Work in Progress_

Unlike Joomla CMS, extensions are not resolved by location in filesystem, but by namespace.

- How extensions communicate between each other? There must be global registry.
- Installation scripts


## AbstractExtension

Abstract extension class. Use it for custom extension types.

### Usage

Helper methods

- `getType`
- `getName`
- `getNamespace`
- `getPath`: get absolute directory path in filesystem
- `getConfig`



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
		$fooComponent = $this->getContainer()->get('e/vendor/fooComponent');

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

_Extensions/vendor/FooComponent_

```
Controller/
    Sub/
	    AlphaController.php
        BetaController.php
    DefaultController.php
Entity/  <-- When using Doctrine/ORM
     Sub.php
Model/  <-- When using Joomla/Model
    SubModel.php
View/  <-- When using Joomla/View
    SubHtmlView.php
templates/
    Sub/
        alpha.view.twig
        beta.view.twig
    layout.html.twig
install/
    doctrine/
        Sub.orm.yml
    config.json
    routes.json
FooComponent.php  <-- Extension
```



## AbstractPlugin

### Usage

```PHP

$dispatcher = $this->getDispatcher();
$extensionServiceProvider = $this->getContainer()->get('e/serviceProvider');

// Get all registered plugins
$plugins = $extensionServiceProvider->findBy(array('type' => 'Plugin'));

// Register each plugin as event listener in dispatcher
foreach ($plugins as $plugin)
{
	$dispatcher->addListerner($plugin, $plugin->getEvents);
}

```

#### The `getEvents` method

Get available events, optionally with defined priorities

#### Suggested file structure

_Extensions/vendor/FooPlugin/_

```
FooPlugin.php  <-- Extension
```



## Extension Service Provider

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

- `$options`: Sources to load extension data from:
  sourceFile: location of json file
  sourceTable: location of database table

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

Concept borrowed from 

- [Symfony2](https://github.com/symfony/symfony/) (Bundles)
- [Joomla CMS](github.com/joomla/joomla-cms/) (Extensions)
- [Joomla-Distro proposal](github.com/joomla-distro/) (Installation)
