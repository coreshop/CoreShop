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

namespace CoreShop\Bundle\StoreBundle\CoreExtension;

use CoreShop\Bundle\StoreBundle\Doctrine\ORM\StoreRepository;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class StoreOptionProvider implements SelectOptionsProviderInterface
{
    /** @var StoreRepository */
    private $repository;

    /**
     * @param StoreRepository $repository
     */
    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     * @return array
     */
    public function getOptions($context, $fieldDefinition)
    {
        $options = [];
        $stores = $this->repository->getAll();
        foreach($stores as $store) {
            $options[] = [
                'key' => $store->getName(),
                'value' => $store->getId()
            ];
        }

        return $options;
    }

    /**
     * Returns the value which is defined in the 'Default value' field
     * @param array $context
     * @param Data $fieldDefinition
     * @return null
     */
    public function getDefaultValue($context, $fieldDefinition)
    {
        return null;
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition)
    {
        return true;
    }
}