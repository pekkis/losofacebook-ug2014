#!/usr/bin/env php
<?php

use Knp\Console\ConsoleEvents;
use Knp\Console\ConsoleEvent;

use Losofacebook\Command\CreateRandomUsersCommand;
use Losofacebook\Command\CreateImagesCommand;
use Losofacebook\Command\AssociateImagesCommand;
use Losofacebook\Command\CreateFriendshipCommand;
use Losofacebook\Command\CreatePostCommand;
use Losofacebook\Command\CreateGaylordLohiposkiCommand;
use Losofacebook\Command\CreateCompaniesCommand;
use Losofacebook\Command\CreateCorporateImagesCommand;

use Losofacebook\Command\InitializeDbCommand;
use Losofacebook\Command\InitializeImagesCommand;


$app = require_once __DIR__ . '/bootstrap.php';

$app['dispatcher']->addListener(ConsoleEvents::INIT, function(ConsoleEvent $event) {
    $app = $event->getApplication();

    $app->add(new InitializeDbCommand());
    $app->add(new InitializeImagesCommand());

    $app->add(new CreateRandomUsersCommand());
    $app->add(new CreateImagesCommand());
    $app->add(new AssociateImagesCommand());
    $app->add(new CreateFriendshipCommand());
    $app->add(new CreatePostCommand());
    $app->add(new CreateGaylordLohiposkiCommand());
    $app->add(new CreateCompaniesCommand());
    $app->add(new CreateCorporateImagesCommand());

});

$application = $app['console'];
$application->run();
