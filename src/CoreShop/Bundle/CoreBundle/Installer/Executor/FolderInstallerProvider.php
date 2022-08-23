<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Installer\Executor;

use Pimcore\Model\DataObject;

final class FolderInstallerProvider
{
    public function __construct(private array $folders)
    {
    }

    public function installFolders(): void
    {
        foreach ($this->folders as $folder) {
            DataObject\Service::createFolderByPath(sprintf('/%s', $folder));
        }
    }
}
