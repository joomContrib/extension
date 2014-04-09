Extension package
=================

_Work in Progress_

Unlike Joomla CMS, extensions are not resolved by location in filesystem, but by namespace.

- How extensions communicate between each other? There must be global registry.
- Installation scripts


## AbstractExtension

Abstract extension class. Use it for custom extension types.

### Usage

#### The `getType` methhod

Get extension type

#### The `getName` method

Get extension name

#### The `getNamespace` method
Get namespace

#### The `getPath` method

Get absolute directory path in filesystem

#### The `getConfig` method

Load extension configuration from database or json file.



### AbstractComponent

Component code (_Extension\[vendor]\FooComponent\FooComponent.php_):

```PHP
namespace Extension\[vendor]\FooComponent;

use joomContrib\Extension\AbstractComponent;

class FooComponent extends AbstractComponent {}
```

Controller code (_Extension\[vendor]\FooComponent\Controller\BarComponent.php_):

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
		// Get extension by alias
		$fooComponent = $this->container->get('e/vendor/fooComponent');

		// Get template file
		$template = $fooComponent->getTemplatePath() . '/' . 'bar.html.php';

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

```
Controller/
    Sub/
	    AlphaController.php
        BetaController.php

    DefaultController.php

Entity/  <-- When using Doctrine\ORM
     Sub.php

Model/  <-- When using Joomla\Model
    SubModel.php

View/  <-- When using Joomla\View
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



### AbstractPlugin

### Usage

#### The `getEvents` method

Get available events, optionally with defined priorities

#### Suggested file structure

```
FooPlugin.php  <-- Extension
```



## Extension Service Provider

### Usage

```PHP
use Joomla\DI\Container;
use joomContrib\Extension\ExtensionServiceProvider;

$container = new Container;

$container->registerServiceProvider(new ExtensionServiceProvider());
```

#### The `__construct` method

**Accepted Parameters**

- `$options`: Sources to load extension data from:
  sourceFile: location of json file
  sourceTable: location of database table

#### The `add` method

- `$extension`: Extension instance to add

#### The `filter` method

@TODO

#### The `byController` method

Return extension of controller

**Accepted Parameters**

- `$controller`: Controller to resolve



## ExtensionInstaller

@TODO

This is hooked up to Composers' [Library Installer](https://github.com/composer/composer/blob/master/src/Composer/Installer/LibraryInstaller.php).
Provides installation/ update/ uninstallation methods, and triggers adequate events (ie. `onBeforeInstall` and `onAfterInstall`).



Disclamer
---------

Concept borrowed from 

- [Symfony2](https://github.com/symfony/symfony/) (Bundles)
- [Joomla CMS](github.com/joomla/joomla-cms/) (Extensions)
- [Joomla-Distro proposal](github.com/joomla-distro/) (Installation)
