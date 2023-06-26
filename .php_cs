<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor');

$config = new PhpCsFixer\Config();
$config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'linebreak_after_opening_tag' => true,
    // Additional rules or overrides can be added here
])
    ->setFinder($finder);

return $config;
