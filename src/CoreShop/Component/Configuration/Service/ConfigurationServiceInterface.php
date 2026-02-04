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
 */

namespace CoreShop\Component\Configuration\Service;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;

interface ConfigurationServiceInterface
{
    /**
     * @return ConfigurationInterface|mixed|null
     */
    public function get(string $key, bool $returnObject = false): mixed;

    public function set(string $key, mixed $data): ConfigurationInterface;

    public function remove(string $key): void;
}
