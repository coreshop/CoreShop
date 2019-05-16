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

use CoreShop\Bundle\ResourceBundle\CoreExtension\Document\ResourceSelect;
use CoreShop\Component\Pimcore\Document\DocumentTagFactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

class ResourceSelectDocumentTagFactory implements DocumentTagFactoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var string
     */
    private $nameProperty;

    /**
     * @param RepositoryInterface $repository
     * @param string $nameProperty
     */
    public function __construct(RepositoryInterface $repository, string $nameProperty)
    {
        $this->repository = $repository;
        $this->nameProperty = $nameProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        return new ResourceSelect($this->repository, $this->nameProperty, $type, $params);
    }
}
