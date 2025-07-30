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

namespace CoreShop\Bundle\ResourceBundle\Form\Registry;

interface FormTypeRegistryInterface
{
    public function add(string $identifier, string $typeIdentifier, string $formType): void;

    public function get(string $identifier, string $typeIdentifier): ?string;

    public function has(string $identifier, string $typeIdentifier): bool;
}
