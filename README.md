# Introduction

This is a default skeleton Web Application that uses F4, a lightweight PHP/PostgreSQL-based web development framework.

# Requirements

To configure and run your app using F4 you will need:

- composer
- node/npm
- php version 8.4.1 or higher
- postgresql version 16 or higher

# Quick start

To start developing your app called `myapp` with F4, use the following composer command:

```
composer create-project f4php/f4 myapp
```

and... that's it, you are good to go! 

Proceed by starting a developemnt server and playing with included Tutorial app.

# Development Server

To run both the built-in PHP development web server and Vite development server, just run the following command:

```
npm run dev
```

You can now open http://localhost:5173 in your browser and proceed with the included Tutorial app.

## More Technical Details on Development Server

The command above has the same effect as running these two commands concurrently:

```
composer run serve
vite
```

By default, `composer run serve` uses `local` environment configuration (note that the concept of F4 environments is explained below), which starts the PHP dev server at `http://localhost:8080`.

To change the host and port configurations, please refer to `composer.json` for PHP dev server and `vite.config.js` for Vite dev server, both files can be found in project root directory.

# Configuring your project

F4 relies on the concept of *environments* to determine how to initialize configuration parameters for your server application.

Environment definitions are read by `F4\Loader` from the main `composer.json` file as an `extra.f4.environments` property.

Each environment must have a name, must be an object and include at least one "config" property, specifying path to a .php file that holds relevant configuration parameters for each environment.

At runtime, F4 will look for `F4_ENVIRONMENT` process variable to decide which environment to use when locating and loading the main configuration file. If no such environment name is specified, it will try the names of `local` and `default` in that particular order.

This is an example of a `local` environment definition taken from `composer.json`:

```json
...
  "extra": {
    "f4": {
      "environments": {
        "local": {
          "config": "config/local.php"
        }
      }
    }
  }

```

All configuration parameters must be implemented as public constants of a `F4\Config` class. This is a very simple example of a `config/local.php` file that redefines `MODULES` constant:

```php
<?php

namespace F4;

class Config extends AbstractConfig
{
    public const array MODULES = [\App\Tutorial\TutorialModule::class];
}
```
Feel free to examine `F4\AbstractConfig` class for all the configuration options supported by F4 Core.

Of course, you are free to invent your own configuration options to suit your application, just ensure that they have unique names to avoid conflict.

## Toolset for Environment Configuration

To customize configuration for your local development needs, or to run your application in a production environment, you will likely find it useful to use an existing configuration as a template and produce a copy. In the process, you might also need tools to substitute values automatically, but more on that later.

Copying an environment configuration can be done with a special command explained below.

### Copying default configuration

This command:

```
composer run create-config
```

will use `default` environment as source, locate and parse its configuration file from the environment definition, strip all the sensitive data, substitute values if necessary and output a PHP-readable file to stdout.

To get started with this tool, you may want to redirect such output and create or overwrite a `config/local.php` file. The following command will generate a configuration file for your `local` environment without stripping anything:

```
composer run create-config -- --keep-sensitive > config/local.php
```

In fact, this is the recommended way to start working on your local configuration parameters, but strictly speaking isn't required because a basic `config/default.php` file is shipped in this distribution and would be used by the loader.

### Use in production

Let's assume you have added a `custom` environment to your `composer.json` and want to use it as a template on a production server. Here's the command to do it:

```
composer run create-config custom > config/production.php
```
But what if you want to automate your deployment without pushing sensitive data to git?

F4's `composer run create-config` command was created to address that exact problem, and it uses a number of special PHP attributes to do it.

Let's imagine your `config/custom.php` file looks like this:

```php
namespace F4;

use F4\Config\{FromIniFile as INI, FromEnvironmentVariable as ENV};

class Config extends AbstractConfig
{
    /**
     * The value of PROD_DB_PASSWORD environment variable will be 
     * substituted when running a create-config command
     * */ 
    #[ENV('PROD_DB_PASSWORD')]
    public const string DB_PASSWORD = '';
    /**
     * The value of PROD_DB_HOST from 'settings.ini' file will be 
     * substituted when running a create-config command
     * */ 
    #[INI('PROD_DB_HOST')]
    public const string DB_HOST = '';
    /**
     * The value of PROD_DB_NAME from 'custom.ini file will be 
     * substituted when running a create-config command
     * */ 
    #[INI('PROD_DB_NAME', 'custom.ini')]
    public const string DB_NAME = '';
}
```
Attribute notation will force `create-config` command to obtain values from specified sources and place them in the generated configuration file.

This powerful mechanism allows you to generate environment-specific configurations using your favorite cloud service without the risk of exposing sensitive data when shipping your code.

Note, however, that generated configuration must be stored as a file on the server (i.e. in a docker container) in order to be parsed by PHP interpreter, although since the entire configuration is implemented as language constants, it's immutable by design. This prevents many attack vectors and therefore has advantage over more traditional .env files.

If you prefer to use .env files to store your configuration parameters, you may still do so in combination with `create-config` feature, following the `#INI(<variable>)` approach described in the example above.

We hope that you and your DevOps engineer will appreciate this little feature.

## Setting Environment in Production

As we already said, F4 will look for `F4_ENVIRONMENT` process variable to decide which environment name to use to locate and load the main configuration file. By default, it will try `local` and `default` in that particular order.

Normally, `production` environment is configured to use `config/production.php` as its main configuration file. This may be changed in your `composer.json`.

We recommend that you stick to `config/production.php` and set up your web server so that the value of `F4_ENVIRONMENT` process variable is set to "production".

Here's an example of how this could be achieved when using Apache web server:
```
<VirtualHost hostname:443>
   ...
   SetEnv F4_ENVIRONMENT production
   ...
</VirtualHost>
```

# Building for production

Building for production is only required for static resources, and the default command is:

```
npm run build
```
**IMPORTANT NOTE:** `public/assets` directory gets completely erased during build process, so make sure not to keep anything important there.

This command will invoke Vite to bundle all the static resources imported with `vite:resource` tags and place them into `public/assets` directory. It will also create a `public/assets/.vite/manifest.json` file for resource reference.

The manifest gets automatically applied to an HTML page by utilizing `vite:bundle` template tag (see below).

## Deployment

Once you have built the assets and established project configuration, be sure to deploy all project files to your production environment and configure the webserver to use `public` directory as its root.

An `.htaccess` file for Apache that forwards all incoming requests to F4 core is already included in this distribution. You may need to apply similar configuration if you use a different web server, such as nginx.

**IMPORTANT NOTE:** Exposing any content outside of `public` directory via webserver (mis-)configuration poses security risks and should be avoided!
