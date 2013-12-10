#!/bin/sh

if [ `puppet --version  | head -c 1` -lt "3" ] ; then
    echo "Upgrading puppet to latest version. Minimally version 3..."

    sudo rpm -ivh http://yum.puppetlabs.com/el/6/products/i386/puppetlabs-release-6-7.noarch.rpm
    sudo yum update
    sudo yum install puppet -y
else
    echo "Puppet already runs version >3"
fi