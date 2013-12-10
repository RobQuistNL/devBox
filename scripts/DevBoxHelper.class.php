<?php
class DevBoxHelper {

    /**
     * @var array Holder for all our settings
     */
    private $settings = array();

    /**
     * @var array Holder for all our packages
     */
    private $packages = array();

    /**
     * A list of vagrantboxes we can use
     * @var array
     */
    private $boxes = array(
        array (
            'humanname' => 'Ubuntu 12.04 x64',
            'name'      => 'precise-64',
            'url'       => 'http://files.vagrantup.com/precise64.box',
        ),
        array (
            'humanname' =>'Ubuntu 12.04 x86',
            'name'      => 'precise-32',
            'url'       => 'http://files.vagrantup.com/precise32.box',
        ),
        array (
            'humanname' => 'CentOS 6.4 x64',
            'name'      => 'centos64-64',
            'url'       => 'http://developer.nrel.gov/downloads/vagrant-boxes/CentOS-6.4-x86_64-v20131103.box',
        ),
        array (
            'humanname' => 'CentOS 6.4 x86',
            'name'      => 'centos64-64',
            'url'       => 'http://developer.nrel.gov/downloads/vagrant-boxes/CentOS-6.4-i386-v20131103.box',
        ),
    );

    /**
     * A list of packages every developer should have.
     * @var array
     */
    private $defaultPackages = array(
        'vim', // I've added both,
        'nano', // Just so we don't get fights. (yet vim comes first)
        'git',
        'bash-completion',
        'make',
        'zip',
        'man',
    );

    /**
     * A list of optional packages
     * @var array
     */
    private $optionalPackages = array(
        'tree',
        'vim-enhanced',
        'htop',
        'screen',
        'strace',
    );

    /**
     * Set a settings
     * @param $key
     * @param $value
     * @return DevBoxHelper
     */
    public function setSetting($key, $value) {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * Return a setting, and if not found, the default.
     * @param $key
     * @param $default
     * @return mixed
     */
    public function getSetting($key, $default = '') {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        } else {
            if ($default == '') {
                trigger_error('Unset setting key without default was fetched!', E_WARNING);
            }
            return $default;
        }
    }

    /**
     * Add a package
     * @param $name
     * @return DevBoxHelper
     */
    public function addPackage($name) {
        $this->packages[] = $name;
        return $this;
    }

    /**
     * Get a short name to describe the machine internally
     */
    public function getShortName()
    {
        $suggestedShortname = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", $this->getSetting('longname')));
        $shortname =  CLI::getLine('Fill in a short name for the machine (a-z A-Z 0-9)', $suggestedShortname);

        //if (preg_match('/[^a-zA-Z0-9]/i', $shortname)) { @todo implement this elsewhere
        //    $this->getShortName();
        //} else {
            $this->setSetting('shortname', $shortname);
        //}
    }

    /**
     * Get the hostname of the machine. The real used hostnames for the web can differ greatly. Doesn't really matter
     * what you fill in here.
     */
    public function getHostname()
    {
        $suggestedHostname = 'dev.' . $this->getSetting('shortname') . '.com';
        $hostname = strtolower(
            CLI::getLine(
                'Fill in a hostname for the machine - server can use other hostnames for web! (valid hostname, no http:// or slashes)',
                $suggestedHostname
            )
        );

        //if (preg_match('/[^A-Za-z\.\-0-9]/i', $hostname)) { @todo implement this elsewhere
        //    $this->getHostname();
        //} else {
            $this->setSetting('hostname', $hostname);
        //}
    }

    /**
     * Get a long descriptive name, for e.g. in VirtualBox manager.
     */
    public function getLongName()
    {
        $longname = CLI::getLine('How do you want to call your machine (no special characters)?', 'My Project');
        //if (preg_match('/[^A-Za-z\-0-9 ]/i', $longname)) { @todo implement this elsewhere
        //    echo 'Please only use A-Z a-z 0-9 - and spaces.' . NL . NL;
        //    $this->getLongName();
        //} else {
            $this->setSetting('longname', $longname);
        //}
    }

    /**
     * Ask the user what IP the box should have
     */
    public function getIp()
    {
        $default = '192.168.56.' . mt_rand(10,240);
        $this->setSetting('ip', CLI::getLine('Please fill in the local IP of your devbox', $default));
    }

    /**
     * Ask the user what box he would like
     */
    public function getBox()
    {
        CLI::line('What Vagrant image do you want to use?');

        $i = 1;
        foreach ($this->boxes as $box) {
            CLI::line('    ' . ($i) . '. ' . $box['humanname']);
            $i++;
        }
        CLI::line();
        $boxnumber = CLI::getLine('Select number', 1);
        if ($boxnumber < 1 || $boxnumber > count($this->boxes)+1) {
            $this->getBox();
        } else {
            $this->setSetting('boxname', $this->boxes[$boxnumber-1]['name']);
            $this->setSetting('boxurl', $this->boxes[$boxnumber-1]['url']);
        }
    }

    /**
     * Ask the user to install a package
     * @param $name
     * @param $default
     */
    public function getPackage($name, $default)
    {
        $answer = CLI::getLine('Install package "' . $name . '"? y/n', $default);
        if ($answer == 'y') {
            $this->addPackage($name);
        } else if ($answer != 'n') {
            $this->getPackage($name, $default);
        }
    }

    /**
     * Ask the user what optional and default packages he would like to install
     */
    public function getInstalledPackages()
    {
        CLI::line('Default packages: ' . implode(', ', $this->defaultPackages));
        $default = CLI::getLine('Install all default packages? (y/n) - Choose "n" to pick your own', 'y');

        foreach ($this->defaultPackages as $package) {
            if ($default == 'n') {
                $this->getPackage($package, 'y');
            } else {
                $this->addPackage($package);
            }
        }

        CLI::line();
        CLI::line('Optional packages: ' . implode(', ', $this->optionalPackages));

        $default = CLI::getLine('Install all optional packages? (y/n) - Choose "n" to pick your own', 'n');
        foreach ($this->optionalPackages as $package) {
            if ($default == 'n') {
                $this->getPackage($package, 'y');
            } else {
                $this->addPackage($package);
            }
        }
    }

    /**
     * Ask the user for an writeable output directory
     */
    public function getOutputDirectory()
    {
        CLI::line('Enter the output directory. This should be the *root* of your project.');
        CLI::line('You can also copy your files afterwards.');
        $outputDirectory = CLI::getLine('Enter writeable, absolute directory', DEFAULT_OUTPUT_DIR);
        $outputDirectory = rtrim($outputDirectory, DIRECTORY_SEPARATOR); //Remove *all* trailing slashes
        $outputDirectory = $outputDirectory . DIRECTORY_SEPARATOR; //Make sure there is a slash

        $this->setSetting('outputdirectory', $outputDirectory);

        if (!is_dir($outputDirectory)) {
            CLI::line('Directory (' . $outputDirectory . ') not found.');
            $this->getOutputDirectory();
        }

        if (!is_writable($outputDirectory)) {
            CLI::line('Directory (' . $outputDirectory . ') must be writable, dummy!');
            $this->getOutputDirectory();
        }
    }

    /**
     * Ask the user for the MB's of memory
     */
    public function getBoxMemory()
    {
        $ram = CLI::getLine('Box RAM in MB\'s', 256);
        if (!is_numeric($ram)) {
            $this->getBoxMemory();
        }

        $this->setSetting('boxmemory', $ram);
    }

    /**
     * Get all the settings we set.
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get a list of all the packages
     * @return array
     */
    public function getPackages()
    {
        return $this->packages;
    }
}