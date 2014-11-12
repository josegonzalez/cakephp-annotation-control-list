[![Build Status](https://travis-ci.org/josegonzalez/cakephp-annotation-control-list.svg?branch=master)](https://travis-ci.org/josegonzalez/cakephp-annotation-control-list) [![Coverage Status](https://img.shields.io/coveralls/josegonzalez/cakephp-annotation-control-list.svg)](https://coveralls.io/r/josegonzalez/cakephp-annotation-control-list?branch=master) [![Total Downloads](https://poser.pugx.org/josegonzalez/cakephp-annotation-control-list/downloads.svg)](https://packagist.org/packages/josegonzalez/cakephp-annotation-control-list) [![Latest Stable Version](https://poser.pugx.org/josegonzalez/cakephp-annotation-control-list/v/stable.svg)](https://packagist.org/packages/josegonzalez/cakephp-annotation-control-list)

# AnnotationControlList Plugin

A simple, annotation-based ACL System for CakePHP

## Background

For the [CakePHP book](http://josediazgonzalez.com/cakephp-book/) I wrote, I thought it would make sense to showcase to users how they might come up with an alternative to the ACL system that comes with CakePHP. As annotations are an interesting way of adding attributes to actions - and it's relatively easy to modify during application development - I decided that a method to do so via annotations would be the way to go.

## Requirements

* PHP 5.4+
* CakePHP 2.x

## Installation

_[Using [Composer](http://getcomposer.org/)]_

Add the plugin to your project's `composer.json` - something like this:

	{
		"require": {
			"josegonzalez/cakephp-annotation-control-list": "1.0.0"
		}
	}

Because this plugin has the type `cakephp-plugin` set in its own `composer.json`, Composer knows to install it inside your `/Plugins` directory, rather than in the usual vendor directory. It is recommended that you add `/Plugins/AnnotationControlList` to your .gitignore file. (Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).)

_[Manual]_

* Download this: [https://github.com/josegonzalez/cakephp-annotation-control-list/zipball/master](https://github.com/josegonzalez/cakephp-annotation-control-list/zipball/master)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `AnnotationControlList`

_[GIT Submodule]_

In your app directory type:

	git submodule add git://github.com/josegonzalez/cakephp-annotation-control-list.git Plugin/AnnotationControlList
	git submodule init
	git submodule update

_[GIT Clone]_

In your plugin directory type

	git clone git://github.com/josegonzalez/cakephp-annotation-control-list.git AnnotationControlList

### Enable plugin

In 2.0 you need to enable the plugin in your `app/Config/bootstrap.php` file:

		CakePlugin::load('AnnotationControlList');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

### Basic Annotation Usage

Setup your `AuthComponent` to use the `AnnotationAuthorize` and `AnnotationFormAuthenticate` classes:

```php
public $components = [
	'Auth' => [
		'authenticate' => [
			'AnnotationControlList.AnnotationForm' => [
				'passwordHasher' => 'Blowfish',
				'roleField' => 'role',  // `roleField` is `role` by default
			]
		],
		'authorize' => [
			'AnnotationControlList.Annotation',
			'roleField' => 'role',  // `roleField` is `role` by default
		],
	]
];
```

#### Requiring roles for a given action

Annotate your methods with the roles you want to allow:

```php
/**
 * @roles all
 */
public function index() {}

/**
 * @roles authenticated
 */
public function add() {}

/**
 * this is a list of roles
 * @roles anonymous, some_other_role
 */
public function register() {}

/**
 * this is also a list of roles
 * @roles ["admin", "a_special_role"]
 */
public function admin() {}
```

You can specify one or more roles in any of the above formats. If no role is specified for an action, then no user will be allowed access.

#### Special Roles

The following roles have a special meaning:

- `all`: All users will have this role
- `anonymous`: Users that have not yet authenticated against your app will have this role
- `authenticated`: Users that have been authenticated fall in this role

#### Available Classes

The following classes are available for your convenience:

- `AnnotationAuthorize`
- `AnnotationBasicAuthenticate`
- `AnnotationBlowfishAuthenticate`
- `AnnotationDigestAuthenticate`
- `AnnotationFormAuthenticate`

These extend the core classes and override the following methods:

- `isAuthorized`
- `getActionRoles`
- `getPrefixedAnnotations`
- `getAnnotations`
- `processRoles`
- `authorize`
- `unauthenticated`
- `getController`
- `prefix`

#### Custom Authenticate Classes

The `AnnotationFormAuthenticate` class extends `FormAuthenticate` to override the `unauthorized()` method, allowing us to use the annotations to define access even if the user has not yet authenticated. You can follow this pattern for any Authenticate class you create/use by adding the following to either your custom authenticate class or a subclass of one of the core classes:

```php
	use AnnotationParser;
```
