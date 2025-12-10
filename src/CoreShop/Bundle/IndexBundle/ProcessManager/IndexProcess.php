<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\ProcessManager;

use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\Pimcore;

final class IndexProcess extends Pimcore
{
    public function run(ExecutableInterface $executable, array $params = null): int
    {
        $settings = $executable->getSettings();

        $settings['command'] = 'coreshop:index';

        $executable->setSettings($settings);

        return parent::run($executable);
    }
}
