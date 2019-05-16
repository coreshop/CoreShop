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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension\DataObject;

trait DISetStateTrait
{
    public static function __set_state($data)
    {
        $thing = \Pimcore::getContainer()->get('pimcore.implementation_loader.object.data')->build($data['fieldtype'], $data);
        $thing->setValues($data);

        return $thing;
    }
}
