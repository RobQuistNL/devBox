<?php
require(__DIR__ . "/scripts/init.php");

$helper = new DevBoxHelper();

CLI::line('Welcome to the DevBox setup helper.');
CLI::line('version ' . VERSION);
CLI::line('DevBox by Enrise - Licensed under Apache2 License');
CLI::line();

$helper->getLongName();
$helper->getShortName();
$helper->getHostName();
$helper->getIp();

CLI::line();
CLI::line('=================================================================================');
CLI::line('=================================================================================');
CLI::line();

$defaultPackages = array(
    'vim', // I've added both,
    'nano', // Just so we don't get fights. (yet vim comes first)
    'git',
    'bash-completion',
    'sl',
    'make',
    'zip',
    'man',
);

CLI::line('Default packages: ' . implode(', ', $defaultPackages));
$default = CLI::getLine('Install all default packages? (y/n) - Choose "n" to pick your own', 'y');

foreach ($defaultPackages as $package) {
    if ($default == 'n') {
        $helper->getPackage($package, 'y');
    } else {
        $helper->addPackage($package);
    }
}
CLI::line();

$optionalPackages = array(
    'tree',
    'vim-enhanced',
    'htop',
    'screen',
    'strace',
);
CLI::line('Default packages: ' . implode(', ', $optionalPackages));

$default = CLI::getLine('Install all optional packages? (y/n) - Choose "n" to pick your own', 'n');
foreach ($optionalPackages as $package) {
    if ($default == 'n') {
        $helper->getPackage($package, 'y');
    } else {
        $helper->addPackage($package);
    }
}

var_dump($helper->getPackages());
var_dump($helper->getSettings());