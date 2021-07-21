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

namespace CoreShop\Bundle\LocaleBundle\DependencyInjection\Compiler;

use CoreShop\Component\Locale\Context\CompositeLocaleContext;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeLocaleContextPass extends PrioritizedCompositeServicePass
{
    public const LOCALE_CONTEXT_SERVICE_TAG = 'coreshop.context.locale';

    public function __construct()
    {
        parent::__construct(
            LocaleContextInterface::class,
            CompositeLocaleContext::class,
            self::LOCALE_CONTEXT_SERVICE_TAG,
            'addContext'
        );
    }
}
