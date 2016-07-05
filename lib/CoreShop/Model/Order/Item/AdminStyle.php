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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Order\Item;

use CoreShop\Model\Order\Item;

/**
 * Class AdminStyle
 * @package CoreShop\Model\Order\Item
 */
class AdminStyle extends \Pimcore\Model\Element\AdminStyle
{
    /**
     * AdminStyle constructor.
     *
     * @param $element
     */
    public function __construct($element)
    {
        parent::__construct($element);

        if ($element instanceof Item) {
            $this->elementIcon = '/pimcore/static/img/icon/page_white_copy.png';
            $this->elementIconClass = null;
        }
    }
}
