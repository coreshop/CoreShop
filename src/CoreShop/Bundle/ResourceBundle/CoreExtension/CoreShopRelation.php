<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\Relations\AbstractRelations;
use Pimcore\Model\Element;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class CoreShopRelation extends Data\ManyToOneRelation
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
        /**
         * @var array $stack
         */
        $stack = \Pimcore::getContainer()->getParameter('coreshop.all.stack');

        return '?\\' . $stack[$this->stack];
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    public function getPhpdocInputType(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    public function getPhpdocReturnType(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    /**
     * @param array $classes
     *
     * @return $this
     */
    public function setClasses($classes)
    {
        if (!empty($this->stack)) {
            $this->classes = Element\Service::fixAllowedTypes(
                $this->getCoreShopPimcoreClasses()[$this->stack],
                'classes'
            );
        }

        return $this;
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
