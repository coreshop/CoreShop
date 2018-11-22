<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\GridConfig;

class GridConfigInstaller implements GridConfigInstallerInterface
{
    /**
     * {@inheritdoc}
     */
    public function installGridConfig($config, $name, $classId, $overwrite = false)
    {
        $list = new GridConfig\Listing();
        $list->addConditionParam('name = ?', $name);
        $elements = $list->load();

        if (count($elements) === 0) {
            $gridConfig = new GridConfig();
        } elseif ($overwrite) {
            $gridConfig = $elements[0];
        } else {
            return;
        }

        $config['classId'] = $classId;

        $configDataEncoded = json_encode($config);
        $gridConfig->setName($name);
        $gridConfig->setShareGlobally(true);
        $gridConfig->setConfig($configDataEncoded);
        $gridConfig->setOwnerId(0);
        $gridConfig->setSearchType('folder');
        $gridConfig->setClassId($classId);
        $gridConfig->save();
    }
}
