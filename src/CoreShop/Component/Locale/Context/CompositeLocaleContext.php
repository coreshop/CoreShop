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

namespace CoreShop\Component\Locale\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeLocaleContext implements LocaleContextInterface
{
    /**
     * @var PriorityQueue|LocaleContextInterface[]
     */
    private $localeContexts;

    public function __construct()
    {
        $this->localeContexts = new PriorityQueue();
    }

    /**
     * @param LocaleContextInterface $localeContext
     * @param int                    $priority
     */
    public function addContext(LocaleContextInterface $localeContext, $priority = 0)
    {
        $this->localeContexts->insert($localeContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
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
