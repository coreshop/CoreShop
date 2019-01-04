<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180905092235 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $invoiceRepo = $this->container->get('coreshop.repository.order_invoice');
        $adjustmentFactory = $this->container->get('coreshop.factory.adjustment');

        $list = $invoiceRepo->getList();
        $list->setUnpublished(true);
        $invoices = $list->load();

        $definition = ClassDefinition::getById($invoiceRepo->getClassId());
        $fieldDefinitions = $definition->fieldDefinitions;

        /**
         * @var $invoice OrderInvoiceInterface
         */
        foreach ($invoices as $invoice) {
            $invoice->removeAdjustments(AdjustmentInterface::SHIPPING);
            $invoice->removeAdjustments(AdjustmentInterface::CART_PRICE_RULE);
            $invoice->removeBaseAdjustments(AdjustmentInterface::SHIPPING);
            $invoice->removeBaseAdjustments(AdjustmentInterface::CART_PRICE_RULE);

            if (array_key_exists('shippingNet', $fieldDefinitions) &&
                array_key_exists('shippingGross', $fieldDefinitions)) {
                if ($invoice->getShippingNet() > 0) {
                    $invoice->addAdjustment(
                        $adjustmentFactory->createWithData(
                            AdjustmentInterface::SHIPPING,
                            '',
                            $invoice->getShippingGross() ?: 0,
                            $invoice->getShippingNet() ?: 0
                        )
                    );
                }
            }

            if (array_key_exists('discountNet', $fieldDefinitions) &&
                array_key_exists('discountGross', $fieldDefinitions)) {
                if ($invoice->getDiscountNet() > 0) {
                    $invoice->addAdjustment(
                        $adjustmentFactory->createWithData(
                            AdjustmentInterface::CART_PRICE_RULE,
                            '',
                            $invoice->getDiscountGross() ?: 0,
                            $invoice->getDiscountNet() ?: 0
                        )
                    );
                }
            }

            if (array_key_exists('baseShippingNet', $fieldDefinitions) &&
                array_key_exists('baseShippingGross', $fieldDefinitions)) {
                if ($invoice->getBaseShippingNet() > 0) {
                    $invoice->addBaseAdjustment(
                        $adjustmentFactory->createWithData(
                            AdjustmentInterface::SHIPPING,
                            '',
                            $invoice->getBaseShippingGross() ?: 0,
                            $invoice->getBaseShippingNet() ?: 0
                        )
                    );
                }
            }

            if (array_key_exists('baseDiscountNet', $fieldDefinitions) &&
                array_key_exists('baseDiscountGross', $fieldDefinitions)) {
                if ($invoice->getBaseDiscountNet() > 0) {
                    $invoice->addBaseAdjustment(
                        $adjustmentFactory->createWithData(
                            AdjustmentInterface::CART_PRICE_RULE,
                            '',
                            $invoice->getBaseDiscountGross() ?: 0,
                            $invoice->getBaseDiscountNet() ?: 0
                        )
                    );
                }
            }

            $invoice->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
