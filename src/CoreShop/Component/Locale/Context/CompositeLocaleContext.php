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

namespace CoreShop\Component\Locale\Context;

use Laminas\Stdlib\PriorityQueue;

final class CompositeLocaleContext implements LocaleContextInterface
{
    /**
     * @var PriorityQueue|LocaleContextInterface[]
     * @psalm-var PriorityQueue<LocaleContextInterface>
     */
    private PriorityQueue $localeContexts;

    public function __construct()
    {
        $this->localeContexts = new PriorityQueue();
    }

    public function addContext(LocaleContextInterface $localeContext, int $priority = 0): void
    {
        $this->localeContexts->insert($localeContext, $priority);
    }

    public function getLocaleCode(): string
    {
        $lastException = null;

        foreach ($this->localeContexts as $localeContext) {
            try {
                return $localeContext->getLocaleCode();
            } catch (LocaleNotFoundException $exception) {
                $lastException = $exception;

                continue;
            }
        }

        throw new LocaleNotFoundException(null, $lastException);
    }
}
