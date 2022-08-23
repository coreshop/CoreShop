<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class GuestAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', AddressType::class)
            ->add(
                'useInvoiceAsShipping',
                CheckboxType::class,
                [
                    'mapped' => false,
                    'label' => 'coreshop.form.address.use_invoice_as_shipping',
                ]
            )
            ->add('invoiceAddress', AddressType::class)
            ->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) {
                /** @var OrderInterface $cart */
                $cart = $event->getData();

                if ($cart->getShippingAddress() && $cart->getInvoiceAddress() &&
                    $cart->getShippingAddress()->getId() !== $cart->getInvoiceAddress()->getId()
                ) {
                    $event->getForm()->get('useInvoiceAsShipping')->setData(false);
                } else {
                    $event->getForm()->get('useInvoiceAsShipping')->setData(true);
                }
            });
    }
}
