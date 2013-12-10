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

//$helper->getModules();

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
@mkdir($outputDirectory . 'dev');
@mkdir($outputDirectory . 'dev/puppet');

$puppetDir = $outputDirectory . 'dev/puppet/';
//@mkdir($puppetDir . 'files');
@mkdir($puppetDir . 'manifests');
@mkdir($puppetDir . 'modules');
@mkdir($puppetDir . 'templates');

CLI::line('Copying puppet files');

copy(
    TEMPLATE_ASSET_PATH . DIRECTORY_SEPARATOR . $helper->getSetting('boxname') . DIRECTORY_SEPARATOR . 'init.sh',
    $puppetDir . 'init.sh'
);

copy(
    TEMPLATE_ASSET_PATH . DIRECTORY_SEPARATOR . 'fileserver.conf',
    $puppetDir . 'fileserver.conf'
);

CLI::line('Generating puppet manifests');
$manifest = new Manifest($helper);
file_put_contents(
    $puppetDir . 'manifests/manifest.pp',
    $manifest->mainManifest()
);

file_put_contents(
    $puppetDir . 'manifests/' . $helper->getSetting('shortname') . '-packages.pp',
    $manifest->packagesManifest()
);

file_put_contents(
    $puppetDir . 'manifests/' . $helper->getSetting('shortname') . '-finalize.pp',
    $manifest->finalizeManifest()
);

CLI::line('All done!');