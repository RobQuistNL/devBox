class {SHORTNAME}::finalize {
    notify {'Finalizing devbox "{LONGNAME}"': }

    class { "timezone":
        timezone => "{TIMEZONE}",
    }
}