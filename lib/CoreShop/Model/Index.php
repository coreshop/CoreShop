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

namespace CoreShop\Model;

use CoreShop\IndexService;
use CoreShop\Plugin;
use CoreShop\Tool;
use Pimcore\Model\Document;

class Index extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

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
     * get Tax by ID
     *
     * @param $id
     * @return Index|null
     */
    public static function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @return Index[]
     */
    public static function getAll()
    {
        $list = new Index\Listing();

        return $list->load();
    }

    /**
     * delete index and workers index structures
     */
    public function delete()
    {
        $worker = IndexService::getIndexService()->getWorker($this->getName());

        if ($worker instanceof IndexService\AbstractWorker) {
            $worker->deleteIndexStructures();
        }

        $this->getDao()->delete();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
