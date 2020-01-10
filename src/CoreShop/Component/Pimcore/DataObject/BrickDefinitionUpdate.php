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

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Model\DataObject;

class BrickDefinitionUpdate extends AbstractDefinitionUpdate
{
    /**
     * @var string
     */
    private $brickKey;

    /**
     * @var DataObject\Objectbrick\Definition
     */
    private $brickDefinition;

    /**
     * @param string $brickKey
     *
     * @throws ClassDefinitionNotFoundException
     */
    public function __construct($brickKey)
    {
        $this->brickKey = $brickKey;
        $this->brickDefinition = DataObject\Objectbrick\Definition::getByKey($brickKey);

        if (is_null($this->brickDefinition)) {
            throw new ClassDefinitionNotFoundException(sprintf('Brick Definition %s not found', $brickKey));
        }

        $this->fieldDefinitions = $this->brickDefinition->getFieldDefinitions();
        $this->jsonDefinition = json_decode(DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->brickDefinition), true);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return DataObject\ClassDefinition\Service::importObjectBrickFromJson($this->brickDefinition, json_encode($this->jsonDefinition), true);
    }
}
