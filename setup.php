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
$helper->getOutputDirectory(DEFAULT_OUTPUT_DIR);

$outputDirectory = $helper->getSetting('outputdirectory');

CLI::line();
if (CLI::getLine('Do you want to continue? ANY FILES IN ' . $outputDirectory . ' MIGHT GET OVERWRITTEN. [y/n]', 'n') == 'n') {
    CLI::line('Phew. Then this will be the end of it.');
    die;
};
CLI::line();

// Now that we have all the information, we'll setup the files.
CLI::line('Setting up vagrantfile.');
$parser = new Parser();
$parser->setTemplate('Vagrantfile');
$parser->setVar($helper->getSettings());
file_put_contents($outputDirectory . 'Vagrantfile', $parser->parse());

CLI::line('Creating folder structure.');
