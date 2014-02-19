# ZF 2-for-1

Version 0.0.1 Created by [Evan Coury](http://blog.evan.pro/) and [Xerkus](https://github.com/Xerkus/).

## Introduction

Original intention of ZF2-for-1 was to provide a compatibility layer for
Zend Framework 1, allowing to use Zend Framework 2 features in ZF1 application.
ZF2-for-1 provides some basic functionality for such integration indeed,
but we believe that migrating to ZF2 is a way to go.

In fact it proved to be quite easy to move (M)VC layer of ZF1 application to
ZF2. And much easier than to run both applications in parallel.  
It is explained by the fact that ZF2 is **very** flexible while ZF1... well,
not.  
This opens possibility for fast straightforward migration, while keeping
most of the application code intact, and for gradual refactoring towards modern
zf2 application afterwards.

To outline said above: current goal of Zf2-for-1 is to reimplement some of the
ZF1 features in ZF2 to make initial migration fast and easy.

## Current Features

### Basic features for zf1 application

* Registers the ZF2 autoloader
* Bootstraps ZF2 configuration and modules
* Makes ZF2 ServiceManager available to zf1 application
* Makes ZF1 application config and bootstrap object available to ServiceManager
* Optionally registers ServiceManager in `Zend_Registry`
* Provides access to ZF2 view helpers in the ZF1 view layer (`$this->zf2Helper('formRow')`
or `$this->zf2Helper()->formRow()`)

### Features for initial migration to ZF2

* Helper class to mimic zf1 request parameters fallback: route -> get -> post
* Set of classes to mimic ContextSwitch behavior
* More coming

## Installation

### Composer install:

* Add to you composer.json
```
"require": {
    "roave/zf2-for-1": "dev-master"
}
```

* Run composer install
* Add this to `application/configs/application.ini`:
```ini
pluginpaths.Zf2for1_Resource     = APPLICATION_PATH "/../vendor/roave/zf2-for-1/src/Zf2for1/Resource"

; This is path where Zf2for1 will be looking by default for zf2 application config
;resources.zf2.config_path = APPLICATION_PATH "/../config/"

;register service manager to Zend_Registry under the key 'service_manager'
resources.zf2.add_sm_to_registry = true
resources.view[] =
```

Example can be found [here](https://github.com/Xerkus/zf2-for-1-example)

### Alternative install:

* Clone this repository into `APPLICATION_PATH/../vendor/Zf2for1` directory.
* Download ZF2 and put the `library/Zend` directory in `APPLICATION_PATH/../vendor/ZF2/`
(Resulting path should be vendor/ZF2/Zend)
* Add this to `application/configs/application.ini`:
```ini
pluginpaths.Zf2for1_Resource     = APPLICATION_PATH "/../vendor/Zf2for1/src/Zf2for1/Resource"

resources.zf2.zf2_path = APPLICATION_PATH "/../vendor/ZF2"
; This is path where Zf2for1 will be looking by default for zf2 application config
;resources.zf2.config_path = APPLICATION_PATH "/../config/"

;register service manager to Zend_Registry under the key 'service_manager'
resources.zf2.add_sm_to_registry = true
resources.view[] =
```

## Plans

There are a lot of ways this could be improved.

* More features
* Usage examples
* Update README

## License

ZF 2-for-1 is released under the New BSD license. See the included LICENSE file.
