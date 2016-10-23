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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\IndexService;

/**
 * Class Index
 * @package CoreShop\Model
 */
class Index extends AbstractModel
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \CoreShop\Model\Index\Config
     */
    public $config;

    /**
     * delete index and workers index structures.
     */
    public function delete()
    {
        $worker = $this->getWorker();

        if ($worker instanceof IndexService\AbstractWorker) {
            $worker->deleteIndexStructures();
        }

        parent::delete();
    }

    /**
     * @return IndexService\AbstractWorker|null
     */
    public function getWorker() {
        return IndexService::getIndexService()->getWorker($this->getName());
    }

    /**
     * @return string
     */
    function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \CoreShop\Model\Index\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \CoreShop\Model\Index\Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
}
