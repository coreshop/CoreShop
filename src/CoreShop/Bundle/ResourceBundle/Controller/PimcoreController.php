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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;

class PimcoreController extends AdminController
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var PimcoreRepositoryInterface
     */
    protected $repository;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param MetadataInterface $metadata
     * @param PimcoreRepositoryInterface $repository
     * @param FactoryInterface $factory
     */
    public function __construct(MetadataInterface $metadata, PimcoreRepositoryInterface $repository, FactoryInterface $factory)
    {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->factory = $factory;
    }
}