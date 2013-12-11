<?php
class Manifest {

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var DevBoxHelper
     */
    private $devboxhelper;

    /**
     * Initialize the manifest class
     * @param DevBoxHelper $devboxhelper
     */
    public function __construct(DevBoxHelper $devboxhelper)
    {
        $this->parser = new Parser();
        $this->devboxhelper = $devboxhelper;
    }

    /**
     * Creates the main puppet manifest.
     *
     * @param DevBoxHelper $devboxhelper
     */
    public function mainManifest()
    {
        $parser = new Parser();
        $parser->setTemplate('manifest.pp');

        //Inherit the default settings
        $parser->setVar($this->devboxhelper->getSettings());

        //:/usr/local/zend/bin
        $parser->setVar('MAN_EXECPATH', '/usr/local/bin:/usr/bin:/bin:/usr/local/sbin:/usr/sbin:/sbin');
        $parser->setVar('CLASSES', '');

        return $parser->parse();
    }

    /**
     * Creates the packages puppet manifest.
     *
     * @param DevBoxHelper $devboxhelper
     */
    public function packagesManifest()
    {
        $parser = new Parser();
        $parser->setTemplate('packages.pp');

        //Inherit the default settings
        $parser->setVar($this->devboxhelper->getSettings());

        //:/usr/local/zend/bin
        $packageString = "'" . implode("', '", $this->devboxhelper->getPackages()) . "'";
        $parser->setVar('PACKAGES', $packageString);

        return $parser->parse();
    }

    /**
     * Creates the packages puppet manifest.
     *
     * @param DevBoxHelper $devboxhelper
     */
    public function finalizeManifest()
    {
        $parser = new Parser();
        $parser->setTemplate('finalize.pp');

        //Inherit the default settings
        $parser->setVar($this->devboxhelper->getSettings());

        if (@date_default_timezone_get()) {
            $parser->setVar('TIMEZONE', @date_default_timezone_get());
        } else {
            $parser->setVar('TIMEZONE', 'Europe/Amsterdam'); //Amsterdam is good.
        }

        return $parser->parse();
    }

    /**
     * Creates an SH file to add needed modules via git.
     *
     * @param DevBoxHelper $devboxhelper
     */
    public function getInstallModulesGit()
    {
        $output = <<<TXT
#!/bin/bash
echo -n "Is this script ran in the project root? [y/n]"
read answer
if [\$answer -ne "y"]; then
    echo "Well, it should. This adds needed git submodules in specific folders!"
endif

TXT;
        foreach ($this->devboxhelper->getModules() as $module) {
            $output .= 'git submodule add ' . $module['repo_url'] . ' dev/puppet/modules/' . $module['shortname'] . PHP_EOL;
        }

        $output .= 'git submodule update --init --recursive' . PHP_EOL;
        $output .= 'echo "All done!"';

        return $output;
    }
}