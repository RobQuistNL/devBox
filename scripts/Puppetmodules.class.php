<?php
class Puppetmodules {

    const folder = './puppetmodules/';

    private $modules = array();

    public function getAvailableModules()
    {
        if (empty($modules)) {
            if ($handle = opendir(self::folder)) {
                while (false !== ($entry = readdir($handle))) {
                    if (is_dir(self::folder . $entry) && $entry != '.' && $entry != '..') {
                        $this->modules[] = $entry;
                    }
                }
            } else {
                throw new Exception('Could not open puppetmodules folder (' . self::folder . ')!');
            }
        }

        return $this->modules;
    }
}