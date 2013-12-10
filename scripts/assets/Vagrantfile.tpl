Vagrant.configure("2") do |config|
    config.vm.define :{SHORTNAME} do |{SHORTNAME}|
        {SHORTNAME}.vm.box = '{BOXNAME}'
        {SHORTNAME}.vm.box_url = '{BOXURL}'
        {SHORTNAME}.vm.network "private_network", ip: "{IP}"
        {SHORTNAME}.vm.hostname = "{HOSTNAME}"
        {SHORTNAME}.vm.provider :virtualbox do |virtualbox|
            virtualbox.customize [
                'modifyvm', :id,
                '--chipset', 'ich9', # solves kernel panic issue on some host machines
                '--memory', '{BOXMEMORY}',
            ]
        end

        {SHORTNAME}.vm.provision :shell, :path => "dev/puppet/init.sh"

        {SHORTNAME}.vm.provision :puppet do |puppet|
            puppet.manifests_path = "dev/puppet/manifests"
            puppet.module_path = "dev/puppet/modules"
            puppet.manifest_file = "manifest.pp"
            puppet.options = [
                '--fileserverconfig', '/vagrant/dev/puppet/fileserver.conf',
                '--templatedir', '/vagrant/dev/puppet/templates/',
                #'--verbose', # Enable for debugging purposes.
                #'--debug', # Enable for debugging purposes.
                '--user', 'puppet',
                '--no-daemonize',
            ]
        end
    end
end