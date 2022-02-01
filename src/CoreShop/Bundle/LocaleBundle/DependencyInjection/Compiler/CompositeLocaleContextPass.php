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

namespace CoreShop\Bundle\LocaleBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeLocaleContextPass extends PrioritizedCompositeServicePass
{
    public const LOCALE_CONTEXT_SERVICE_TAG = 'coreshop.context.locale';

    public function __construct()
    {
        parent::__construct(
            'coreshop.context.locale',
            'coreshop.context.locale.composite',
            self::LOCALE_CONTEXT_SERVICE_TAG,
            'addContext'
        );
    }
}
