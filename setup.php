<?php
require(__DIR__ . "/scripts/init.php");

$helper = new DevBoxHelper();

CLI::line('Welcome to the DevBox setup helper.');
CLI::line('version ' . VERSION);
CLI::line('DevBox by Enrise - Licensed under Apache2 License');
CLI::line();

$helper->getBox();
$helper->getBoxMemory();

$helper->getLongName();
$helper->getShortName();
$helper->getHostName();
$helper->getIp();

CLI::line();
CLI::line('=================================================================================');
CLI::line('=================================================================================');
CLI::line();

$helper->getInstalledPackages();
$helper->getOutputDirectory();



// Now that we have all the information, we'll setup the files.
$parser = new Parser();
$parser->setTemplate('Vagrantfile');
$parser->setVar($helper->getSettings);

$outputDirectory = $helper->getSetting('outputdirectory');

file_put_contents($outputDirectory . 'Vagrantfile', $parser->parse());
