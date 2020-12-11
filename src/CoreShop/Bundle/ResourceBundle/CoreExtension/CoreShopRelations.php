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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model\DataObject\ClassDefinition\Data;

class CoreShopRelations extends Data\ManyToManyRelation
{
    public $fieldtype = 'coreShopRelation';

    public $stack;

    public $relationType = true;

    public $objectsAllowed = true;

    public $assetsAllowed = false;

    public $documentsAllowed = false;

    public function getStack()
    {
        return $this->stack;
    }

    public function setStack($stack): void
    {
        $this->stack = $stack;
    }

    protected function getCoreShopPimcoreClasses()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.all.stack.pimcore_class_names');
    }

    public function getParameterTypeDeclaration(): ?string
    {
        return '?array';
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return '?array';
    }

    public function getPhpdocInputType(): ?string
    {
        return '?\\' . \Pimcore::getContainer()->getParameter('coreshop.all.stack')[$this->stack] . '[]';
    }

    public function getPhpdocReturnType(): ?string
    {
        return '?\\' . \Pimcore::getContainer()->getParameter('coreshop.all.stack')[$this->stack] . '[]';
    }

    public function getClasses()
    {
        $classes = $this->getCoreShopPimcoreClasses()[$this->stack];
        $return = [];

        foreach ($classes as $cl) {
            $return[] = [
                'classes' => $cl
            ];
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function getObjectsAllowed()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getDocumentsAllowed()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getDocumentTypes()
    {
        return [];
    }

    /**
     *
     * @return bool
     */
    public function getAssetsAllowed()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getAssetTypes()
    {
        return [];
    }
}
