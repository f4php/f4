<?php

namespace F4;

use F4\AbstractConfig;

class Config extends AbstractConfig
{
    public const array MODULES = [
        \App\Tutorial\TutorialModule::class,
    ];
    public const bool DEBUG_MODE = true;
    public const bool TEMPLATE_CACHE_ENABLED = !self::DEBUG_MODE;
    public const array TEMPLATE_PATHS = [
        __DIR__ . '/../templates'
    ];
    public const string DEFAULT_TEMPLATE = 'get_started.pug';
    public const array LOCALES = [
        'en' => [
            'extensions'    => ['.en'],
            'resources'     => ['../templates/locales/get_started.en.ftl'],
            'weight'        => 1
        ],
        'ru' => [
            'extensions'    => ['.ru'],
            'resources'     => ['../templates/locales/get_started.ru.ftl'],
            'weight'        => 0.5
        ],
    ];
}