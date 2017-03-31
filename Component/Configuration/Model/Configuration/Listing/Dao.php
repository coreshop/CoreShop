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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Configuration\Model\Configuration\Listing;

use CoreShop\Component\Configuration\Model\Configuration;
use CoreShop\Component\Core\Model\Listing\Dao\AbstractDao;
use CoreShop\Component\Core\Model\Listing\ListingInterface;
use Pimcore\Model\Dao\PhpArrayTable;

class Dao extends PhpArrayTable
{
     /**
     * @return bool|string
     */
    private function getModelClass() {
        if ($this->model instanceof ListingInterface) {
            return $this->model->getModelClass();
        }

        return false;
    }

    /**
     * configure.
     */
    public function configure()
    {
        parent::configure();
        $this->setFile('coreshop_configurations');
    }

    /**
     * Loads a list of Configurations for the specicifies parameters, returns an array of Configuration elements.
     *
     * @return array
     */
    public function load()
    {
        $modelClass = $this->getModelClass();

        $routesData = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());

        $routes = [];
        foreach ($routesData as $routeData) {
            $routes[] = $modelClass::getById($routeData['id']);
        }

        $this->model->setData($routes);

        return $routes;
    }

    /**
     * get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        $data = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());
        $amount = count($data);

        return $amount;
    }
}
