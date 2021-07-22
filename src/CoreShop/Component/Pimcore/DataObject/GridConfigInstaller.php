<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\GridConfig;

class GridConfigInstaller implements GridConfigInstallerInterface
{
    public function installGridConfig(array $config, string $name, string $class, bool $overwrite = false): void
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

        $config['classId'] = $class;

        $configDataEncoded = json_encode($config);
        $gridConfig->setName($name);
        $gridConfig->setShareGlobally(true);
        $gridConfig->setConfig($configDataEncoded);
        $gridConfig->setOwnerId(0);
        $gridConfig->setSearchType('folder');
        $gridConfig->setClassId($class);
        $gridConfig->save();
    }
}
