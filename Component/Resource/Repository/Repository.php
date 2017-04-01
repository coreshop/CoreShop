<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Core\Model\Listing\ListingInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

/**
 * Interface RepositoryInterface
 * @package CoreShop\Component\Core\Repository
 */
class Repository implements RepositoryInterface {

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var FactoryInterface
     */
    private $listFactory;

    /**
     * @param string $modelClass
     * @param FactoryInterface $listFactory
     */
    public function __construct($modelClass, FactoryInterface $listFactory)
    {
        $this->modelClass = $modelClass;
        $this->listFactory = $listFactory;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id) {
        $model = $this->modelClass;

        return $model::getById($id);
    }

    /**
     * @param $id
     * @param $shopId
     * @return mixed
     */
    public function getByShopId($id, $shopId) {
        $model = $this->modelClass;

        return $model::getByShopId($id, $shopId);
    }

    /**
     * @return ListingInterface
     */
    public function getList() {
        return $this->listFactory->createNew();
    }
}