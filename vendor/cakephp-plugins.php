<?php
$baseDir = dirname(dirname(__FILE__));

return [
    'plugins' => [
        'AuditStash' => $baseDir . '/vendor/lorenzo/audit-stash/',
        'Authentication' => $baseDir . '/vendor/cakephp/authentication/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'CakePdf' => $baseDir . '/vendor/friendsofcake/cakepdf/',
        'Cake/TwigView' => $baseDir . '/vendor/cakephp/twig-view/',
        'CsvView' => $baseDir . '/vendor/friendsofcake/cakephp-csvview/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Josegonzalez/Upload' => $baseDir . '/vendor/josegonzalez/cakephp-upload/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'ReCrud' => $baseDir . '/plugins/ReCrud/',
        'ReCrud - Copy' => $baseDir . '/plugins/ReCrud - Copy/',
        'Search' => $baseDir . '/vendor/friendsofcake/search/',
        'Shim' => $baseDir . '/vendor/dereuromark/cakephp-shim/',
        'Tools' => $baseDir . '/vendor/dereuromark/cakephp-tools/',
    ],
];
