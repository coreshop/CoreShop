<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Bundle\RuleBundle\Form\Type\AbstractConfigurableRuleElementType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Money values of rule conditions/actions submitted by Pimcore Studio are integers (minor units).
 *
 * Studio's rule editors load a rule's configuration as stored (money as integers, e.g. 772 for 7.72) and send
 * it back as it is, while MoneyType expects the display value (7.72) and multiplies it by the decimal factor.
 * Without this, every Studio save of a rule multiplied its money values by the decimal factor (772 -> 77200),
 * also for values nobody touched. The Studio "coreshop_money" widget (CoreBundle, money-widget module) keeps
 * the integer in the form and only displays the decimal value.
 *
 * Scope: only requests to the Studio API and only MoneyType fields inside a rule condition/action
 * (AbstractConfigurableRuleElementType: shipping, product price, product specific price, cart price and
 * payment provider rules). The classic admin keeps submitting display values, and the Studio data types that
 * submit display values themselves (store values, quantity price ranges) are not inside a rule element.
 */
final class StudioRuleMoneyTypeExtension extends AbstractTypeExtension
{
    public const STUDIO_API_PATH_PREFIX = '/pimcore-studio/api/';

    public function __construct(
        private RequestStack $requestStack,
        private int $decimalFactor,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $value = $event->getData();

            if (!$this->isMinorUnitInteger($value)) {
                return;
            }

            if (!$this->isStudioRequest() || !$this->isInsideRuleElement($event->getForm())) {
                return;
            }

            $event->setData($this->toDisplayValue((int) $value));
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [MoneyType::class];
    }

    private function isMinorUnitInteger(mixed $value): bool
    {
        if (\is_int($value)) {
            return true;
        }

        return \is_string($value) && 1 === preg_match('/^-?\d+$/', $value);
    }

    private function isStudioRequest(): bool
    {
        $request = $this->requestStack->getMainRequest();

        return null !== $request && str_starts_with($request->getPathInfo(), self::STUDIO_API_PATH_PREFIX);
    }

    /**
     * @param FormInterface<mixed> $form
     */
    private function isInsideRuleElement(FormInterface $form): bool
    {
        for ($parent = $form->getParent(); null !== $parent; $parent = $parent->getParent()) {
            if ($parent->getConfig()->getType()->getInnerType() instanceof AbstractConfigurableRuleElementType) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exact decimal string, e.g. 772 -> "7.72", -5 -> "-0.05", so MoneyType's transformer restores the integer.
     */
    private function toDisplayValue(int $minorUnits): string
    {
        $digits = (int) round(log10($this->decimalFactor));
        if ($digits <= 0 || 10 ** $digits !== $this->decimalFactor) {
            return (string) ($minorUnits / $this->decimalFactor);
        }

        $sign = $minorUnits < 0 ? '-' : '';
        $absolute = str_pad((string) abs($minorUnits), $digits + 1, '0', \STR_PAD_LEFT);

        return $sign . substr($absolute, 0, -$digits) . '.' . substr($absolute, -$digits);
    }
}
