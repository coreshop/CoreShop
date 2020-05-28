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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

class RegisterIndexWorkerPass extends RegisterRegistryTypePass
{
    public const INDEX_WORKER_TAG = 'coreshop.index.worker';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.index.worker',
            'coreshop.form_registry.index.worker',
            'coreshop.index.workers',
            self::INDEX_WORKER_TAG
        );
    }
}
