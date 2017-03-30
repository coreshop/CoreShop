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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Visitor;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Visitor;

/**
 * Class Page
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Visitor
 */
class Page extends AbstractModel
{
    /**
     * @var int
     */
    public $visitorId;

    /**
     * @var Visitor
     */
    public $visitor;

    /**
     * @var string
     */
    public $controller;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $module;

    /**
     * @var string
     */
    public $referrer;

    /**
     * @var int
     */
    public $creationDate;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s) (%s) (%s) (%s) (%s)", $this->getVisitorId(), $this->getController(), $this->getAction(), $this->getModule(), $this->getReferrer(), $this->getId());
    }

    /**
     * @return int
     */
    public function getVisitorId()
    {
        return $this->visitorId;
    }

    /**
     * @param int $visitorId
     */
    public function setVisitorId($visitorId)
    {
        $this->visitorId = $visitorId;
    }

    /**
     * @return Visitor
     */
    public function getVisitor()
    {
        if (!$this->visitor instanceof Visitor) {
            $this->visitor = Visitor::getById($this->visitor);
        }

        return $this->visitor;
    }

    /**
     * @param Visitor $visitor
     *
     * @throws Exception
     */
    public function setVisitor($visitor)
    {
        if (!$visitor instanceof Visitor) {
            throw new Exception('$visitor must be instance of Visitor');
        }

        $this->visitor = $visitor;
        $this->visitorId = $visitor->getId();
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
}
