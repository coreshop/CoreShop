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
 * Class Source
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Visitor
 */
class Source extends AbstractModel
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
     * @var int
     */
    public $pageId;

    /**
     * @var Page
     */
    public $page;

    /**
     * @var string
     */
    public $referrer;

    /**
     * @var string
     */
    public $requestUrl;

    /**
     * @var int
     */
    public $creationDate;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getPageId(), $this->getId());
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
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        if (!$this->page instanceof Page) {
            $this->page = Page::getById($this->page);
        }

        return $this->page;
    }

    /**
     * @param Page $page
     *
     * @throws Exception
     */
    public function setPage($page)
    {
        if (!$page instanceof Page) {
            throw new Exception('$page must be instance of Page');
        }

        $this->page = $page;
        $this->pageId = $page->getId();
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
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * @param string $requestUrl
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
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
