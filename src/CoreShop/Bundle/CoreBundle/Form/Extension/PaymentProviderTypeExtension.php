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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class PaymentProviderTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('stores', StoreChoiceType::class, [
            'multiple' => true,
        ]);
    }

    public function getExtendedType(): string
    {
        return PaymentProviderType::class;
    }

    public static function getExtendedTypes(): array
    {
        return [PaymentProviderType::class];
    }
}
