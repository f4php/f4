#!/usr/bin/env php
<?php

declare(strict_types=1);

use F4\Loader;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

require_once __DIR__ . "/../vendor/autoload.php";

(new SingleCommandApplication())
    ->setDescription('Prints a complete environment-specific PHP file with an F4\Config class definition inside. The main purpose of this utility is to generate configuration based on environmetn variables or ini files.')
    ->addArgument('environment', InputArgument::OPTIONAL, 'Source environment name from composer.json', 'default')
    ->addOption('keep-sensitive-data', null, InputOption::VALUE_NONE, 'Include data marked as sensitive')
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        Loader::setPath(path: __DIR__ . '/../');
        Loader::loadEnvironmentConfig(environments: [$input->getArgument('environment')]);
        $output->writeln(Loader::generateConfigurationFile(comment: 'NOTE: This file was generated using create-config script', stripSensitiveData: $input->getOption('keep-sensitive-data') !== true));
        return SingleCommandApplication::SUCCESS;
    })
    ->run();
