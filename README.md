# ZF 2-for-1

Version 0.0.1 Created by [Evan Coury](http://blog.evan.pro/)

## Introduction

ZF 2-for-1 provides a compatibility layer for Zend Framework 1, allowing you to
use Zend Framework 2 features in your ZF1 application. For example, if you want
to upgrade from the old Zend\_From to ZF2's Zend\\Form, but can't afford to
refactor your entire application for ZF2, **this is for you**. This works by
registering a Zend\_Application resource, thus it requires Zend Framework 1.8 or
later. At the moment it's only been tested with 1.12.1.

Enjoy responsibly.

## Current features

* Registers the ZF2 autoloader
* Bootstraps ZF2 configuration and modules
* Makes available the ZF2 view helpers in the ZF1 view layer (`$this->zf2Helper('formRow')`
or `$this->zf2Helper()->formRow()`)

## Installation

This process should be simplified, but for now here's how you can get it working:

* Clone this repository.
* Copy the `src/Zf2for1` directory into your application's `library/` directory.
* Copy the `zf2/` directory into your application's APPLICATION\_PATH (usually `./application/`).
* Download ZF2 and put the `Zend/` directory in `APPLICATION_PATH/zf2/vendor`

Add this to your `application.config.php`:

```ini
autoloaderNamespaces[]           = "Zf2for1_"
autoloaderNamespaces[]           = "Zf2for1\\"
pluginPaths.Zf2for1_Resource     = "Zf2for1/Resource"
resources.zf2.zf2Path            = APPLICATION_PATH "/zf2/vendor"
resources.zf2.configPath         = APPLICATION_PATH "/zf2/config"
resources.zf2.add_sm_to_registry = true
resources.view[] =
```

You can of course change the paths to your liking.

## Plans

There are a lot of ways this could be improved.

* Better README / installation instructions and improved installation process (plus compatibility with composer)
* The view helper implementation should be set up to handle `__invoke()` properly
* We could attach to the route event and actually dispatch ZF2 if the request isn't for ZF1
* We could create a ZF1 controller plugin for the service manager, possibly.
* Whatever else you might think of.

I accept pull requests. :)

## License

ZF 2-for-1 is released under the New BSD license. See the included LICENSE file.
