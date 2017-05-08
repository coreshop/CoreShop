<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\ResourceBundle\Repository;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

class PimcoreRepository implements PimcoreRepositoryInterface
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $className = $this->metadata->getClass('model');

        return $className::getList();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $className = $this->metadata->getClass('model');

        return $className::getById($id);
    }
}
