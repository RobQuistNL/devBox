<?php
class DevBoxHelper {

    private $settings = array();
    private $packages = array();

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

    public function getHostname()
    {
        $suggestedHostname = 'dev.' . $this->getSetting('shortname') . '.com';
        $hostname = strtolower(
            CLI::getLine(
                'Fill in a hostname for the machine (valid hostname, no http:// or slashes)',
                $suggestedHostname
            )
        );

        //if (preg_match('/[^A-Za-z\.\-0-9]/i', $hostname)) { @todo implement this elsewhere
        //    $this->getHostname();
        //} else {
            $this->setSetting('hostname', $hostname);
        //}
    }

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

    public function getIp()
    {
        $default = '192.168.56.' . mt_rand(10,240);
        $this->setSetting('ip', CLI::getLine('Please fill in the local IP of your devbox', $default));
    }

    public function getPackage($name, $default)
    {
        $answer = CLI::getLine('Install package "' . $name . '"? y/n', $default);
        if ($answer == 'y') {
            $this->addPackage($name);
        } else if ($answer != 'n') {
            $this->getPackage($name, $default);
        }
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getPackages()
    {
        return $this->packages;
    }
}