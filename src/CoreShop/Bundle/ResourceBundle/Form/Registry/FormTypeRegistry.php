<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Form\Registry;

final class FormTypeRegistry implements FormTypeRegistryInterface
{
    private array $formTypes = [];

    public function add(string $identifier, string $typeIdentifier, string $formType): void
    {
        $this->formTypes[$identifier][$typeIdentifier] = $formType;
    }

    public function get(string $identifier, string $typeIdentifier): ?string
    {
        if (!$this->has($identifier, $typeIdentifier)) {
            return null;
        }

        return $this->formTypes[$identifier][$typeIdentifier];
    }

    public function has(string $identifier, string $typeIdentifier): bool
    {
        return isset($this->formTypes[$identifier][$typeIdentifier]);
    }
}
