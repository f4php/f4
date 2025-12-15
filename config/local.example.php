<?php

namespace F4;

use F4\AbstractConfig;
use F4\Config\FromEnvironmentVariable as ENV;
use F4\Config\FromIniFile as INI;

class Config extends AbstractConfig
{
    #[ENV(name: 'F4_DEBUG_MODE')]
    public const bool DEBUG_MODE = true;

    #[INI(name: 'TEMPLATE_CACHE_LIFETIME', file: 'local-settings.ini')]
    public const int TEMPLATE_CACHE_LIFETIME = parent::TEMPLATE_CACHE_LIFETIME;

}
