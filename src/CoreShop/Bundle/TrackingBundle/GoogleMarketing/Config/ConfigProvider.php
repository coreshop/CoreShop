<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 * Originally derived from pimcore/google-marketing-bundle (POCL).
 */

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\Config;

class ConfigProvider
{
    private ?Config $config = null;

    /**
     * @param array<string, mixed>|null $configObject
     */
    public function __construct(private ?array $configObject = null)
    {
    }

    public function getConfig(): Config
    {
        if (null === $this->config) {
            $this->config = new Config($this->getConfigObject());
        }

        return $this->config;
    }

    /**
     * @return array<string, mixed>
     */
    private function getConfigObject(): array
    {
        if (null === $this->configObject) {
            $this->configObject = $this->loadDefaultConfigObject();
        }

        return $this->configObject;
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadDefaultConfigObject(): array
    {
        return [];
    }
}
