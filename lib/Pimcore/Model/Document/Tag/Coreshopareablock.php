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

namespace Pimcore\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\ExtensionManager;
use Pimcore\Tool;
use Pimcore\Model\Document;

class Coreshopareablock extends Areablock
{

    /**
     * get type
     *
     * @see Document\Tag\TagInterface::getType
     * @return string
     */
    public function getType()
    {
        return "coreshopareablock";
    }

    /**
     * is custom are path
     *
     * @return bool
     */
    public function isCustomAreaPath()
    {
        return true;
    }

    /**
     * get area directory
     *
     * @return string
     */
    public function getAreaDirectory()
    {
        return CORESHOP_TEMPLATE_PATH . "/areas";
    }
}
