<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return (new Config())
    ->setRules([
        '@Symfony' => true,
    'phpdoc_separation' => false,
    'phpdoc_align' => false,
    'phpdoc_order' => false,
    'phpdoc_trim' => false,
    'phpdoc_no_empty_return' => false,
    'no_superfluous_phpdoc_tags' => false,
    
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
