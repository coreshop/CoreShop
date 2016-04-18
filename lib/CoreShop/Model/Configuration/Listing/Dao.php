<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Configuration\Listing;

use Pimcore;
use CoreShop\Model;

class Dao extends Pimcore\Model\Dao\PhpArrayTable
{

    /**
     * configure
     */
    public function configure()
    {
        parent::configure();
        $this->setFile("coreshop_configurations");
    }

    /**
     * Loads a list of Configurations for the specicifies parameters, returns an array of Configuration elements
     *
     * @return array
     */
    public function load()
    {
        $routesData = $this->db->fetchAll($this->model->getFilter(), $this->model->getOrder());

        $routes = array();
        foreach ($routesData as $routeData) {
            $routes[] = Model\Configuration::getById($routeData["id"]);
        }

        $this->model->setConfigurations($routes);
        return $routes;
    }

    /**
     * get total count
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
