<?php
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRules([
        '@PSR12' => true,
        // aquí puedes añadir reglas extras si quieres
    ])
    ->setFinder(
        Finder::create()
            // apunta a tu carpeta de código
            ->in(__DIR__ . '/src')
    );
