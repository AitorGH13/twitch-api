<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('lumen-api');

return (new Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder);
