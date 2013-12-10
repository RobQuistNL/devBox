class {SHORTNAME}::packages {
    notify {'Installing packages': }

    package { [{PACKAGES}] :
        ensure => installed,
    }

    class { "ntp": }

}