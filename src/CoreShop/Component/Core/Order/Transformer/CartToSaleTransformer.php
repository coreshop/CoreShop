<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use Pimcore\Model\DataObject\Objectbrick;
use Webmozart\Assert\Assert;

final class CartToSaleTransformer implements ProposalTransformerInterface
{
    /**
     * @var ProposalTransformerInterface
     */
    private $innerCartToOrderTransformer;

    /**
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     */
    public function __construct(
        ProposalTransformerInterface $innerCartToOrderTransformer
    ) {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
    }

    /**
     * @param ProposalInterface $cart
     * @param ProposalInterface $sale
     *
     * @return ProposalInterface|mixed
     */
    public function transform(ProposalInterface $cart, ProposalInterface $sale)
    {
        /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($sale, SaleInterface::class);

        $sale = $this->innerCartToOrderTransformer->transform($cart, $sale);

        if ($sale instanceof QuoteInterface || $sale instanceof OrderInterface) {
            if ($cart->getCarrier() instanceof CarrierInterface) {
                $sale->setCarrier($cart->getCarrier());
                $sale->setComment($cart->getComment());

                if ($sale instanceof PaymentSettingsAwareInterface) {
                    $sale->setPaymentSettings($cart->getPaymentSettings());
                }

                $sale->setShippingTaxRate($cart->getShippingTaxRate());
            } else {
                $sale->setShippingTaxRate(0);
            }

            $cartAdditionalData = $cart->getAdditionalData();
            $saleAdditionalData = $sale->getAdditionalData();

            // transfer cart additional data to sale additional data
            if ($cartAdditionalData instanceof Objectbrick &&
                $saleAdditionalData instanceof Objectbrick) {
                foreach ($saleAdditionalData->getAllowedBrickTypes() as $brickType) {
                    if (in_array($brickType, $cartAdditionalData->getAllowedBrickTypes())) {
                        $brickSetter = 'set' . ucfirst($brickType);
                        $brickGetter = 'get' . ucfirst($brickType);
                        $saleAdditionalData->$brickSetter($cartAdditionalData->$brickGetter());
                    }
                }

                $sale->setAdditionalData($saleAdditionalData);
            }

            $sale->setWeight($cart->getWeight());
            $sale->save();
        }

        return $sale;
    }
}
