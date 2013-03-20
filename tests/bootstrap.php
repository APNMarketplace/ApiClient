<?php

if (!file_exists(__DIR__.'/../composer.lock')) {
    echo 'Dependencies must be installed using composer:'."\n\n";
    echo 'php composer.phar install --dev'."\n\n";
    echo 'See http://getcomposer.org for help with installing composer'."\n";
    die;
}

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('ApnMarketplace\ApiClient', __DIR__.'/../src/');
