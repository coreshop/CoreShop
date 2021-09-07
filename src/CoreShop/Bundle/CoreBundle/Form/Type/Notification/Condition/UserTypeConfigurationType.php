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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition;

use CoreShop\Component\Core\Notification\Rule\Condition\User\UserTypeChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class UserTypeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userType', ChoiceType::class, [
                'choices' => [
                    UserTypeChecker::TYPE_PASSWORD_RESET,
                    UserTypeChecker::TYPE_REGISTER,
                    UserTypeChecker::TYPE_NEWSLETTER_DOUBLE_OPT_IN,
                    UserTypeChecker::TYPE_NEWSLETTER_CONFIRMED,
                ],
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_notification_condition_user_type';
    }
}
