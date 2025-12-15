# Introduction

This is a default skeleton Web Application that uses F4, a lightweight PHP/PostgreSQL-based web development framework.

# Requirements

To configure and run your app using F4 you will need:

- composer
- node/npm
- php version 8.3 or higher
- postgresql version 13 or higher

# Quick start

To start developing your app called `myapp` with F4, use the following composer command:

```
$ composer create-project f4php/f4 myapp
```

then initialize the dependencies by running:
```
$ composer install
$ npm install
```
and... that's it, you are good to go! 

Proceed by starting a developemnt server and playing with included Web Application Developer's Guide.

# Development Server

To run both the built-in PHP development web server and Vite development server, just run the following command:

```
$ npm run dev
```

You can now open http://localhost:5173 in your browser and proceed with the Web Application Developer's Guide.

## More Technical Details on Development Server

The command above has the same effect as running these two commands concurrently:

```
$ composer run serve
$ vite
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
    public const array MODULES = [App\Example::class];
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
$ composer run create-config
```

will use `default` environment as source, locate and parse its configuration file from the environment definition, strip all the sensitive data, substitute values if necessary and output a PHP-readable file to stdout.

To get started with this tool, you may want to redirect such output and create or overwrite a `config/local.php` file. The following command will generate a configuration file for your `local` environment without stripping anything:

```
$ composer run create-config -- --keep-sensitive > config/local.php
```

In fact, this is the recommended way to start working on your local configuration parameters, but strictly speaking isn't required because a basic `config/default.php` file is shipped in this distribution and would be used by the loader.

### Use in production

Let's assume you have added a `custom` environment to your `composer.json` and want to use it as a template on a production server. Here's the command to do it:

```
$ composer run create-config custom > config/production.php
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
$ npm run build
```
**IMPORTANT NOTE:** `public/assets` directory gets completely erased during build process, so make sure not to keep anything important there.

This command will invoke Vite to bundle all the static resources imported with `vite:resource` tags and place them into `public/assets` directory. It will also create a `public/assets/.vite/manifest.json` file for resource reference.

The manifest gets automatically applied to an HTML page by utilizing `vite:bundle` template tag (see below).

## Deployment

Once you have built the assets and established project configuration, be sure to deploy all project files to your production environment and configure the webserver to use `public` directory as its root.

An `.htaccess` file for Apache that forwards all incoming requests to F4 core is already included in this distribution. You may need to apply similar configuration if you use a different web server, such as nginx.

**IMPORTANT NOTE:** Exposing any content outside of `public` directory via webserver (mis-)configuration poses security risks and should be avoided!

# Template Syntax for Integrating with Vite

F4 works great with Vite both in production and development modes. It does this out of the box by providing `vite-plugin-f4` and supporting custom `vite:` tags in pug templates.

The specification below defines the syntax for including and customizing JavaScript, CSS, and Vue Single-File Component (SFC) resources in Pug templates. It integrates with Vite for efficient resource bundling and development server support.

## 1. Declaring Page-Specific Resources

The `vite:resource` tag allows developers to declare (or, effectively, include) JavaScript, CSS, Stylus or Vue SFCs in a pug template. These declarations are used by Vite to locate and bundle the resources.

Syntax:

```pug
vite:resource(src="<path>" [bundle="<bundle_name>"])
vite:resource(src="<path_to_sfc.vue>" element="<element_name>" [bundle="<bundle_name>"] )
vite:resource(src="<path_to_sfc.vue>" name="<component_name>" [bundle="<bundle_name>"] )
```

Examples:

```pug
vite:resource(src="path/to/script.js") // "default" bundle name
vite:resource(src="path/to/style.css") // is assumed here

vite:resource(src="path/to/another_style.styl" bundle="my_page")

vite:resource(src="path/to/component.vue" element="my-component" bundle="my_page")
vite:resource(src="path/to/component.vue" name="myComponent" bundle="my_page")
```

`vite:resource` can be placed anywhere in a pug template and is used by Vite to extract virtual entry points.

Either `element` or `name` attribute is required for Vue SFCs imports, if only the `name` attribute is provided, you will have to mount the component manually.

The `bundle` attribute is optional ('default' is assumed if omitted).

## 2. Including Bundled Resources

The `vite:bundle` tag includes pre-built bundles in the rendered HTML, with support for custom attributes on the generated `<script>` and `<link>` tags.

Syntax:

```pug
vite:bundle([name="<bundle_name>"])
```
or
```pug
vite:bundle([name="<bundle_name>"])
  script([attributes])
  link([attributes])
```

## Behavior:

`vite:bundle` tag: Specifies the bundle import point, normally in a page's `<head>` tag. Bundle name may be added by using the optional `name` attribute ("default" is assumed if omitted).

Child `script` and `link` elements (optional): Define script and link tags to customize attributes for specific resources in the bundle.

Default Behavior: If no child elements are defined, the tag generates default `<script>` and `<link>` tags for all resources in the bundle.

The path to specific resources is determined automatically from Vite's `manifest.json` file in production mode, and programmatically constructed at runtime in dev mode.

## Examples:

### Default inclusion

```pug
vite:bundle
```

Rendered production HTML:

```html
<script src="/assets/default.abcdef01.js" type="module"></script>
<link href="/assets/default.abcdef01.css" rel="stylesheet">
```
### Named bundle inclusion

```pug
vite:bundle(name="main")
```

Rendered production HTML:

```html
<script src="/assets/main.abcdef01.js" type="module"></script>
<link href="/assets/main.abcdef01.css" rel="stylesheet">
```

### Customizing attributes

```pug
vite:bundle(name="main")
  script(async defer)
  link(rel="preload" as="style")
```

Rendered production HTML:

```html
<script src="/assets/main.abcdef01.js" type="module" async defer></script>
<link href="/assets/main.abcdef01.css" rel="preload" as="style">
```

### Customizing specific resource types

```pug
vite:bundle(name="main")
  script(async)
```

Rendered production HTML:

```html
<script src="/assets/main.abcdef01.js" type="module" async></script>
<link href="/assets/main.abcdef01.css" rel="stylesheet">
```

## 3. Development Server Integration

In development mode, the `vite:bundle` tag automatically switches to support Vite dev server with Hot Module Replacement (HMR) enabled.

**IMPORTANT NOTE** Vite config included in this distribution provides automatic mapping for all dependencies listed in package.json's `dependencies` property, so make sure that all modules required by your application are listed there, and not as `devDependencies`, otherwide Vite's development server will throw various resolution errors.

**TECHNICAL NOTE**: In order for PHP code to run in dev mode, `Config::DEBUG_MODE` must be set to true, and a `X-Vite-Devserver` header must be present in every request to PHP dev server. This behavior is enabled by `vite-plugin-f4`, which configures Vite dev server to also act as a proxy to PHP dev server.

Example:

```pug
vite:bundle(name="main")
```

Rendered HTML in Development Mode:

```html
<script src="/@vite/client" type="module"></script>
<script src="/@id/__x00__virtual:f4/main.js" type="module"></script>
```

## 4. Summary of Tags and Attributes

### Tags

| Tag               | Purpose       | Attributes    | Child Elements |
| ----------------- | ------------- | ------------- | -------------- |
| `vite:resource`    | Declares page-specific JS/CSS/SFC resources for inclusion.	| `src` (required), `name` or `element` (required for .vue SFCs), `bundle` (optional)	| -
| `vite:bundle`       | Includes bundled resources in the HTML.	| `name` (optional)	 | `script`, `link` |

#### Attributes for `vite:resource`:

`src`: Specifies path to a resource to be included in a bundle.

`element`: Specifies a custom element name to apply a Vue SFC imported from `src`. This or `name` is required for SFC resources, ignored otherwise.

`name`: Specifies Vue SFC component variable name for mounting manually. This or `element` is required for SFC resources, ignored otherwise.

`bundle` (optional): Specifies bundle name ("default" is assumed if omitted).

#### Attributes for `vite:bundle`:

`name` (optional): Specifies bundle name ("default" is assumed if omitted).

#### Child Elements for `vite:bundle`:

`script`: Customizes `<script>` tag attributes (e.g., async, defer).

`link`: Customizes `<link>` tag(s) attributes (e.g., rel, as).

### Notes on internal processing

`vite:resource` is parsed by Vite and completely stripped from output by the PHP Pug renderer.

`vite:bundle` is completely ignored by Vite and expanded by the PHP Pug renderer.

Here's a markdown documentation for the Core API based on the `CoreApiInterface`:

# F4 Framework Core API Documentation

## Overview
The Core API provides the main interface for interacting with the F4 Framework. It handles routing, request/response lifecycle, hooks, debugging, and other core functionality.

## Core API Methods

### Request & Response Handling

#### `setRequest(RequestInterface $request): static`
Sets the current request object.
- **Parameters:**
  - `$request`: PSR-7 compatible request object
- **Returns:** Self instance for method chaining

#### `getRequest(): RequestInterface`
Gets the current request object.
- **Returns:** Current PSR-7 compatible request object

#### `setResponse(ResponseInterface $response): static`
Sets the current response object.
- **Parameters:**
  - `$response`: PSR-7 compatible response object
- **Returns:** Self instance for method chaining

#### `getResponse(): ResponseInterface`
Gets the current response object.
- **Returns:** Current PSR-7 compatible response object

### Routing

#### `addRoute(Route|string $routeOrPath, ?callable $handler = null): Route`
Adds a new route to the application.
- **Parameters:**
  - `$routeOrPath`: Either a Route object or a string path pattern
  - `$handler`: Optional callback to handle the route
- **Returns:** Route instance
- **Example:**
```php
// Using string path
$f4->addRoute('GET /users', function() {
    return ['users' => []];
});

// Using Route object
$route = new Route('GET /users/{id}', function($id) {
    return ['user' => $id];
});
$f4->addRoute($route);
```

#### `addRouteGroup(RouteGroup $routeGroup): RouteGroup`
Adds a group of routes with shared attributes.
- **Parameters:**
  - `$routeGroup`: RouteGroup instance containing grouped routes
- **Returns:** RouteGroup instance
- **Example:**
```php
$group = new RouteGroup('/api/v1', function() use ($f4) {
    $f4->addRoute('GET /users', 'UserController::list');
    $f4->addRoute('POST /users', 'UserController::create');
});
$f4->addRouteGroup($group);
```

### Middleware & Hooks

#### `setRequestHandler(callable $handler): static`
Sets middleware to process requests before routing.
- **Parameters:**
  - `$handler`: Callback function to handle incoming requests
- **Returns:** Self instance for method chaining
- **Alias:** `before()`

#### `setResponseHandler(callable $handler): static`
Sets middleware to process responses before sending.
- **Parameters:**
  - `$handler`: Callback function to handle outgoing responses
- **Returns:** Self instance for method chaining
- **Alias:** `after()`

#### `addHook(string $hookName, callable $callback): static`
Adds a hook to extend framework functionality at specific points.
- **Parameters:**
  - `$hookName`: Name of the hook to attach to
  - `$callback`: Function to execute when hook is triggered
- **Returns:** Self instance for method chaining
- **Example:**
```php
$f4->addHook('beforeRoute', function() {
    // Execute before route handling
});
```

### Exception Handling

#### `addExceptionHandler(string $exceptionClassName, callable $handler): static`
Adds custom exception handler for specific exception types.
- **Parameters:**
  - `$exceptionClassName`: Full class name of exception to handle
  - `$handler`: Callback to handle the exception
- **Returns:** Self instance for method chaining
- **Alias:** `on()`
- **Example:**
```php
$f4->addExceptionHandler(ValidationException::class, function($e) {
    return ['error' => $e->getMessage()];
});
```

### Response Formatting

#### `setResponseFormat(string $format): static`
Sets the format for the response output.
- **Parameters:**
  - `$format`: MIME type or format identifier
- **Returns:** Self instance for method chaining

#### `getResponseFormat(): string`
Gets the current response format.
- **Returns:** Current format as string

### Template Handling

#### `setTemplate(string|callable $template, ?string $format = null): static`
Sets the template for rendering the response.
- **Parameters:**
  - `$template`: Template name/path or custom resolver
  - `$format`: Optional format override
- **Returns:** Self instance for method chaining

#### `getTemplate(?string $format = null): string`
Gets the current template name/path.
- **Parameters:**
  - `$format`: Optional format to get template for
- **Returns:** Template name/path as string

### System Configuration

#### `setTimezone(string $timezone): static`
Sets the application timezone.
- **Parameters:**
  - `$timezone`: Valid PHP timezone identifier
- **Returns:** Self instance for method chaining

### Component Access

#### `getRouter(): RouterInterface`
Gets the router instance.
- **Returns:** Current router implementation

#### `getDebugger(): DebuggerInterface`
Gets the debugger instance when in debug mode.
- **Returns:** Current debugger implementation

## Best Practices

1. Always use type hints and return type declarations
2. Chain methods where appropriate for fluent interfaces
3. Use dependency injection instead of global state
4. Handle exceptions appropriately
5. Use PSR-7 compatible request/response objects

## Error Handling

The framework provides several ways to handle errors:

1. Exception handlers for specific exception types
2. Global error handling through PHP error handlers
3. Debug mode for detailed error information
4. Custom error templates

## Configuration

Core functionality can be configured through:

1. Environment variables
2. Configuration files
3. Runtime configuration
4. Bootstrap process

## Examples

### Basic Application Setup
```php
$f4 = new \F4\Core();

// Add routes
$f4->addRoute('GET /', function() {
    return ['message' => 'Hello World'];
});

// Add middleware
$f4->before(function($request) {
    // Process request
    return $request;
});

// Add exception handling
$f4->on(Exception::class, function($e) {
    return ['error' => $e->getMessage()];
});

// Run application
$f4->run();
```

### API Setup
```php
$f4 = new \F4\Core();

// Set JSON response format
$f4->setResponseFormat('application/json');

// Group API routes
$f4->addRouteGroup(new RouteGroup('/api', function() use ($f4) {
    $f4->addRoute('GET /users', 'UserController::list');
    $f4->addRoute('POST /users', 'UserController::create');
}));

$f4->run();
```