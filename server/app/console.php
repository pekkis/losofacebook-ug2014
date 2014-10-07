#!/usr/bin/env php
<?php

use Knp\Console\ConsoleEvents;
use Knp\Console\ConsoleEvent;

use Losofacebook\Command\CreateRandomUsersCommand;
use Losofacebook\Command\CreateImagesCommand;
use Losofacebook\Command\CreatePostCommand;
use Losofacebook\Command\CreateGaylordLohiposkiCommand;

use Losofacebook\Command\InitializeDbCommand;
use Losofacebook\Command\InitializeImagesCommand;


$app = require_once __DIR__ . '/bootstrap.php';

$app['dispatcher']->addListener(ConsoleEvents::INIT, function(ConsoleEvent $event) {
    $app = $event->getApplication();

    $app->add(new InitializeDbCommand());
    $app->add(new InitializeImagesCommand());

    $app->add(new CreateRandomUsersCommand());
    $app->add(new CreateImagesCommand());
    $app->add(new CreatePostCommand());
    $app->add(new CreateGaylordLohiposkiCommand());
});

$application = $app['console'];
$application->run();
