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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
abstract class Multiselect extends Model\DataObject\ClassDefinition\Data\Multiselect
{
    public function isDiffChangeAllowed($object, $params = []): bool
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = []): ?array
    {
        return [];
    }

    /**
     * @param mixed $object
     * @param array $params
     *
     * @return mixed
     */
    public function preGetData($object, $params = [])
    {
        if (!$object instanceof Model\AbstractModel) {
            return null;
        }

        $data = $object->getObjectVar($this->getName());

        // Don't convert null to empty array to allow Pimcore's inheritance to work
        // null means "not set" and will inherit from parent
        // empty array means "explicitly set to no values" and won't inherit
        return $data;
    }

    /**
     * Checks if data is empty. Returns true only for null, not for empty arrays.
     * This allows differentiating between "not set" (null) and "explicitly empty" ([]).
     *
     * @param array|null $data
     *
     * @return bool
     */
    public function isEmpty($data): bool
    {
        // Only null is considered empty (will inherit from parent)
        // Empty array [] is NOT empty (explicitly set to no values, won't inherit)
        return null === $data;
    }
}
