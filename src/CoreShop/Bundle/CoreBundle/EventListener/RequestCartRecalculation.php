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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Http\RequestHelper;
use Pimcore\Model\Version;
use Pimcore\Service\Context\PimcoreContextGuesser;
use Pimcore\Tool;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestCartRecalculation
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var RequestHelper
     */
    private $pimcoreRequestHelper;

    /**
     * @param CartManagerInterface $cartManager
     * @param ConfigurationServiceInterface $configurationService
     * @param RequestHelper $pimcoreRequestHelper
     */
    public function __construct(
        CartManagerInterface $cartManager,
        ConfigurationServiceInterface $configurationService,
        RequestHelper $pimcoreRequestHelper
    )
    {
        $this->cartManager = $cartManager;
        $this->configurationService = $configurationService;
        $this->pimcoreRequestHelper = $pimcoreRequestHelper;
    }

    /**
     * Force Cart to be recalculated
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event) {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->pimcoreRequestHelper->isFrontendRequest($event->getRequest())) {
            return;
        }

        $cart = $this->cartManager->getCart();

        //This could lead to performance issues,
        //The main issue is: when should a cart get recalculated
        //when a price-rule gets invalid? we actually don't know
        //when a price-rule changes or gets invalid
        //Maybe a CronJob to recalculate carts every 30 min?
        if ($cart->getId()) {
            if ($this->configurationService->get('SYSTEM.PRICE_RULE.UPDATE') > $cart->getModificationDate()) {
                Version::disable();
                $cart->save();
                Version::enable();
            }
        }
    }
}