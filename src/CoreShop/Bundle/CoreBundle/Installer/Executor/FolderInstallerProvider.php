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

namespace CoreShop\Bundle\CoreBundle\Installer\Executor;

use Pimcore\Model\DataObject;

final class FolderInstallerProvider
{
    /**
     * @var array
     */
    private $folders;

    /**
     * @param array $folders
     */
    public function __construct(array $folders) {
        $this->folders = $folders;
    }

    /**
     * Installs all CoreShop needed Folders.
     */
    public function installFolders()
    {
        foreach ($this->folders as $folder) {
            DataObject\Service::createFolderByPath(sprintf('/%s', $folder));
        }
    }
}
