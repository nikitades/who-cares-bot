<?php

include 'php-cs-fixer-rules/PhpdocOrderThrowsAtTheEndFixer.php';

use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderThrowsAtTheEndFixer;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/../projects')
    ->notPath('Infrastructure/Kernel.php')
;

return PhpCsFixer\Config::create()
    ->setCacheFile('var/cache/dev/.phpcsfixer.cache')
    ->registerCustomFixers([new PhpdocOrderThrowsAtTheEndFixer()])
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => false,
        'array_syntax' => ['syntax' => 'short'],
        'no_superfluous_phpdoc_tags' => false,
        'no_unused_imports' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'phpdoc_order' => true,
        'Lala/phpdoc_order_throws_at_the_end_fixer' => true,
        'phpdoc_separation' => false,
        'phpdoc_align' => false,
        'doctrine_annotation_indentation' => true,
        'no_extra_blank_lines' => ['tokens' => [
            'curly_brace_block',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block',
            'throw',
        ]],
    ])
    ->setFinder($finder);
