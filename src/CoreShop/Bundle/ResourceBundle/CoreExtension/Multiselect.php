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

        if (null === $data) {
            $data = [];
        }

        return $data;
    }
}
