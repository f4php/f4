<?php

declare(strict_types=1);

use F4\Loader;
use F4\Core;

error_reporting(error_level: E_ALL & ~E_DEPRECATED);

require_once __DIR__.'/../vendor/autoload.php';

Loader::setPath(path: __DIR__ . '/../');
Loader::setPublicPath(publicPath: __DIR__);
Loader::loadEnvironmentConfig(environments: [($_SERVER['F4_ENVIRONMENT']??null)?:'local', 'default']);

new Core(/*
        $alternativeCoreApiProxyClassName,
        $alternativeRouterClassName,
        $alternativeDebuggerClassName,
    */)
    ->setUpRequestResponse(
        /*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->setUpEnvironment(
        /*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->setUpLocalizer(
        /*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->setUpEmitter(
        /*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->registerModules(/*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->processRequest(/*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->emitResponse(/*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ->restoreEnvironment(/*
        function($defaultHandler) {
            // This place is for dirty hacks only, please refer to class structure for better customization options
            // $this refers to Core instance
            $defaultHandler();
        }
    */)
    ;