<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class RegisterInstallersPass extends RegisterSimpleRegistryTypePass
{
    public const INSTALLER_TAG = 'coreshop.resource.installer';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.resource.installers',
            'coreshop.resource.installers',
            self::INSTALLER_TAG
        );
    }
}
