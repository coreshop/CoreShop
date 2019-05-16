<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\Pimcore;

use CoreShop\Bundle\ResourceBundle\CoreExtension\DataObject\ResourceMultiselect;
use CoreShop\Component\Pimcore\DataObject\ObjectDataFactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

class ResourceMultiselectObjectTagFactory implements ObjectDataFactoryInterface
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param string $model
     * @param RepositoryInterface $repository
     */
    public function __construct(string $model, RepositoryInterface $repository)
    {
        $this->model = $model;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        return new ResourceMultiselect($type, $this->model, $this->repository, $params);
    }
}
