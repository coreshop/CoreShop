<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Form\Registry;

final class FormTypeRegistry implements FormTypeRegistryInterface
{
    /**
     * @var array
     */
    private $formTypes = [];

    /**
     * {@inheritdoc}
     */
    public function add(string $identifier, string $typeIdentifier, string $formType): void
    {
        $this->formTypes[$identifier][$typeIdentifier] = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifier, string $typeIdentifier): ?string
    {
        if (!$this->has($identifier, $typeIdentifier)) {
            return null;
        }

        return $this->formTypes[$identifier][$typeIdentifier];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier, string $typeIdentifier): bool
    {
        return isset($this->formTypes[$identifier][$typeIdentifier]);
    }
}
