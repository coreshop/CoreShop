<?php
declare(strict_types=1);

namespace CoreShop\Test\PHPUnit\Suites\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\Cart;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributorInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Shipping\Taxation\ShippingTaxationCartItems;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use Pimcore\Model\DataObject\Fieldcollection\Data\CoreShopTaxItem;
use Prophecy\Argument;

class ShippingTaxationCartItemsTest extends Base
{
    /** @test */
    public function withoutCartItemsReturnInputTaxes(): void
    {
        $this->printTestName();

        $shippingTaxationCartItems = new ShippingTaxationCartItems(
            $this->prophesize(TaxCollectorInterface::class)->reveal(),
            $this->prophesize(TaxCalculatorFactoryInterface::class)->reveal(),
            $this->prophesize(ProportionalIntegerDistributorInterface::class)->reveal()
        );

        $cart = $this->prophesize(CartInterface::class);
        $cart->getItems()
            ->willReturn([]);

        $shippingTaxes = $shippingTaxationCartItems->calculateShippingTax(
            $cart->reveal(),
            $this->prophesize(Carrier::class)->reveal(),
            $this->prophesize(AddressInterface::class)->reveal(),
            []
        );

        self::assertSame([], $shippingTaxes);
    }

    /** @test */
    public function calculateShippingTaxByUsingCartItemsAsUnitTest(): void
    {
        $this->printTestName();

        $shippingGross = 1200;
        $shippingNetInitially = 1000;
        $taxAmountProduct1 = 36;
        $taxAmountProduct2 = 90;
        $shippingTaxSum = $taxAmountProduct1 + $taxAmountProduct2; // 126
        $shippingNetAfterCalculation = $shippingGross - $shippingTaxSum; // 1074

        $taxItem01 = new CoreShopTaxItem();
        $taxItem01->setName('20');
        $taxItem01->setAmount($taxAmountProduct1);
        $taxItem02 = new CoreShopTaxItem();
        $taxItem02->setName('10');
        $taxItem02->setAmount($taxAmountProduct2);

        /** @var Cart $cart */
        $cart = Data::createCartWithProductsWithDifferentTaxRules();
        $product1Gross = $cart->getitems()[0]->getTotal(true); // 1800, Tax Rate = 20
        $product2Gross = $cart->getitems()[1]->getTotal(true); // 8250, Tax Rate = 10

        $shippingAdjustment = $cart->getAdjustments(AdjustmentInterface::SHIPPING)[0];
        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetInitially, $shippingAdjustment->getPimcoreAmountNet());


        $taxCollectorMock = $this->prophesize(TaxCollectorInterface::class);
        $taxCollectorMock->collectTaxesFromGross(Argument::any(), 215)
            ->willReturn([$taxItem01]);
        $taxCollectorMock->mergeTaxes([$taxItem01], [])
            ->willReturn([$taxItem01]);
        $taxCollectorMock->collectTaxesFromGross(Argument::any(), 985)
            ->willReturn([$taxItem02]);
        $taxCollectorMock->mergeTaxes([$taxItem02], [$taxItem01])
            ->willReturn([$taxItem01, $taxItem02]);

        $taxCalculationFactoryMock = $this->prophesize(TaxCalculatorFactoryInterface::class);
        $taxCalculatorMock = $this->prophesize(TaxCalculatorInterface::class);
        $taxCalculationFactoryMock->getTaxCalculatorForAddress(Argument::any(), Argument::any())
            ->willReturn($taxCalculatorMock->reveal());

        $distributorMock = $this->prophesize(ProportionalIntegerDistributorInterface::class);
        $distributorMock->distribute([$product1Gross, $product2Gross], $shippingGross)
            ->shouldBeCalled()
            ->willReturn([215, 985]);

        $shippingTaxationCartItems = new ShippingTaxationCartItems(
            $taxCollectorMock->reveal(),
            $taxCalculationFactoryMock->reveal(),
            $distributorMock->reveal()
        );

        $shippingTaxes = $shippingTaxationCartItems->calculateShippingTax(
            $cart,
            $this->prophesize(Carrier::class)->reveal(),
            $this->prophesize(AddressInterface::class)->reveal(),
            []
        );

        self::assertSame([$taxItem01, $taxItem02], $shippingTaxes);
        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetAfterCalculation, $shippingAdjustment->getPimcoreAmountNet());
    }

    /** @test */
    public function calculateShippingTaxByUsingCartItemsAsFunctionalTest(): void
    {
        $this->printTestName();

        $shippingGross = 1200;
        $shippingNetInitially = 1000;
        $taxAmountProduct1 = 36;
        $taxAmountProduct2 = 90;
        $shippingTaxSum = $taxAmountProduct1 + $taxAmountProduct2; // 126
        $shippingNetAfterCalculation = $shippingGross - $shippingTaxSum; // 1074

        /** @var Cart $cart */
        $cart = Data::createCartWithProductsWithDifferentTaxRules();

        $shippingAdjustment = $cart->getAdjustments(AdjustmentInterface::SHIPPING)[0];
        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetInitially, $shippingAdjustment->getPimcoreAmountNet());

        /** @var ShippingTaxationCartItems $shippingTaxationCartItemsService */
        $shippingTaxationCartItemsService = $this->get('coreshop.shipping.tax.strategy.cart_items');

        /** @var CoreShopTaxItem[] $usedTaxes */
        $usedTaxes = $shippingTaxationCartItemsService->calculateShippingTax(
            $cart,
            new Carrier(),
            $cart->getShippingAddress(),
            []
        );

        self::assertCount(2, $usedTaxes);
        foreach ($usedTaxes as $taxItem) {
            if ($taxItem->getName() === '20') {
                self::assertSame($taxAmountProduct1, $taxItem->getAmount());
            }
            if ($taxItem->getName() === '10') {
                self::assertSame($taxAmountProduct2, $taxItem->getAmount());
            }
        }

        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetAfterCalculation, $shippingAdjustment->getPimcoreAmountNet());
    }

    /** @test */
    public function calculateShippingTaxByUsingCartWithOneItemMustReturnTaxRate20(): void
    {
        $this->printTestName();

        $shippingGross = 1200;
        $shippingNetInitially = 1000;
        $taxAmount = 200;
        $shippingNetAfterCalculation = $shippingGross - $taxAmount; // 1000 (equal to initial value)

        /** @var Cart $cart */
        $cart = Data::createCartWithProductsWithDifferentTaxRules();
        $cart->removeItem($cart->getItems()[1]);

        $shippingAdjustment = $cart->getAdjustments(AdjustmentInterface::SHIPPING)[0];
        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetInitially, $shippingAdjustment->getPimcoreAmountNet());

        /** @var ShippingTaxationCartItems $shippingTaxationCartItemsService */
        $shippingTaxationCartItemsService = $this->get('coreshop.shipping.tax.strategy.cart_items');

        /** @var CoreShopTaxItem[] $usedTaxes */
        $usedTaxes = $shippingTaxationCartItemsService->calculateShippingTax(
            $cart,
            new Carrier(),
            $cart->getShippingAddress(),
            []
        );

        self::assertCount(1, $usedTaxes);
        self::assertSame($taxAmount, \array_pop($usedTaxes)->getAmount());

        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetAfterCalculation, $shippingAdjustment->getPimcoreAmountNet());
    }

    /** @test */
    public function calculateShippingTaxByUsingCartWithOneItemMustReturnTaxRate10(): void
    {
        $this->printTestName();

        $shippingGross = 1200;
        $shippingNetInitially = 1000;
        $taxAmount = 109;
        $shippingNetAfterCalculation = $shippingGross - $taxAmount; // 1091

        /** @var Cart $cart */
        $cart = Data::createCartWithProductsWithDifferentTaxRules();
        $cart->removeItem($cart->getItems()[0]);

        $shippingAdjustment = $cart->getAdjustments(AdjustmentInterface::SHIPPING)[0];
        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetInitially, $shippingAdjustment->getPimcoreAmountNet());

        /** @var ShippingTaxationCartItems $shippingTaxationCartItemsService */
        $shippingTaxationCartItemsService = $this->get('coreshop.shipping.tax.strategy.cart_items');

        /** @var CoreShopTaxItem[] $usedTaxes */
        $usedTaxes = $shippingTaxationCartItemsService->calculateShippingTax(
            $cart,
            new Carrier(),
            $cart->getShippingAddress(),
            []
        );

        self::assertCount(1, $usedTaxes);
        self::assertSame($taxAmount, \array_pop($usedTaxes)->getAmount());

        self::assertSame($shippingGross, $shippingAdjustment->getPimcoreAmountGross());
        self::assertSame($shippingNetAfterCalculation, $shippingAdjustment->getPimcoreAmountNet());
    }

    /** @test */
    public function missingShippingAdjustmentMustReturnInputTaxes(): void
    {
        $this->printTestName();

        /** @var Cart $cart */
        $cart = Data::createCartWithProductsWithDifferentTaxRules();

        $cart->removeAdjustments(AdjustmentInterface::SHIPPING);

        /** @var ShippingTaxationCartItems $shippingTaxationCartItemsService */
        $shippingTaxationCartItemsService = $this->get('coreshop.shipping.tax.strategy.cart_items');

        /** @var CoreShopTaxItem[] $usedTaxes */
        $usedTaxes = $shippingTaxationCartItemsService->calculateShippingTax(
            $cart,
            new Carrier(),
            $cart->getShippingAddress(),
            []
        );

        self::assertSame([], $usedTaxes);
    }
}
