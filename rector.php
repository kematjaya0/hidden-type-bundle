<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        // getName() stays: renaming it to getBlockPrefix() would change the form theme block names
        RenameMethodRector::class => [
            __DIR__ . '/src/Type',
        ],
    ])
    ->withPhpSets(php83: true)
    ->withSets([
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ])
    ->withComposerBased(symfony: true)
    ->withPreparedSets(deadCode: true, typeDeclarations: true)
    ->withImportNames(importShortClasses: false, removeUnusedImports: true);
