<?php

namespace CoreShop\Bundle\CoreBundle\Application;

final class Version {
    const BUILD_VERSION = '1';
    const MAJOR_VERSION = '2';
    const MINOR_VERSION = '0';
    const RELEASE_VERSION = '0';
    const EXTRA_VERSION = 'pre-alpha.1';

    /**
     * @return string
     */
    public static function getVersion() {
        $version = sprintf('%s.%s.%s', Version::MAJOR_VERSION, Version::MINOR_VERSION, Version::RELEASE_VERSION);

        if (Version::EXTRA_VERSION) {
            $version = sprintf('%s.%s', $version, Version::EXTRA_VERSION);
        }

        return $version;
    }

    public static function getBuild() {
        return Version::BUILD_VERSION;
    }
}
