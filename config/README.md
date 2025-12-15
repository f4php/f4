## Running dev mode

The following command will start two web servers: a built-in php web server to process requests, and vite's web server to track changes and proxy requests to php backend. An address of vite server will be displayed, so you can just open it in the browser and start working on your code, vite will reload the page when you save source files.

```
composer run serve
```

To run php server only without autorefresh capability, you may use the following command:

```
composer run serve
```

If you want to change the parameters of development web servers, please check `composer.json`, host and port configuration is stored as part of "extra.f4.environments" section under "local" environment.