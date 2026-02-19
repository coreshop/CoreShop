/**
 * CoreShop CoreBundle StudioForm Demo Tab Extensions
 *
 * Registers Core-specific demo tabs in the StudioForm demo widget.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

interface DemoTabDefinition {
  key: string
  label: string
  blockPrefix: string
  description: string
  phpSource: string
}

interface WindowWithCoreShopDemoTabs extends Window {
  coreshopStudioFormDemoTabs?: DemoTabDefinition[]
}

const ENTITY_CHOICES_PHP = `final class EntityChoiceDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', CountryChoiceType::class, [
                'label' => 'Country (Single)',
                'required' => false,
            ])
            ->add('countries', CountryChoiceType::class, [
                'label' => 'Countries (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('state', StateChoiceType::class, [
                'label' => 'State (Single)',
                'required' => false,
            ])
            ->add('states', StateChoiceType::class, [
                'label' => 'States (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('zone', ZoneChoiceType::class, [
                'label' => 'Zone',
                'required' => false,
            ])
            ->add('zones', ZoneChoiceType::class, [
                'label' => 'Zones (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'Currency (Single)',
                'required' => false,
            ])
            ->add('currencies', CurrencyChoiceType::class, [
                'label' => 'Currencies (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('store', StoreChoiceType::class, [
                'label' => 'Store',
                'required' => false,
            ])
            ->add('stores', StoreChoiceType::class, [
                'label' => 'Stores (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('paymentProvider', PaymentProviderChoiceType::class, [
                'label' => 'Payment Provider (Single)',
                'required' => false,
            ])
            ->add('paymentProviders', PaymentProviderChoiceType::class, [
                'label' => 'Payment Providers (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('taxRate', TaxRateChoiceType::class, [
                'label' => 'Tax Rate (Single)',
                'required' => false,
            ])
            ->add('taxRates', TaxRateChoiceType::class, [
                'label' => 'Tax Rates (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('taxRuleGroup', TaxRuleGroupChoiceType::class, [
                'label' => 'Tax Rule Group (Single)',
                'required' => false,
            ])
            ->add('taxRuleGroups', TaxRuleGroupChoiceType::class, [
                'label' => 'Tax Rule Groups (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_entity_choices';
    }
}`

const entityChoicesDemoTab: DemoTabDefinition = {
  key: 'entity-choices',
  label: 'Entity Choices',
  blockPrefix: 'coreshop_demo_entity_choices',
  description: 'CoreShop entity ChoiceTypes rendered via EntityChoiceWidget: Country, State, Zone, Currency, Store, Payment Provider, Tax Rate, Tax Rule Group - with single and multi-select variants.',
  phpSource: ENTITY_CHOICES_PHP,
}

export const registerCoreStudioFormDemoTabs = (): void => {
  const windowWithCoreShopDemoTabs = window as WindowWithCoreShopDemoTabs
  const existingTabs = windowWithCoreShopDemoTabs.coreshopStudioFormDemoTabs ?? []

  const entityChoicesTabExists = existingTabs.some((tab) => {
    return tab.key === entityChoicesDemoTab.key || tab.blockPrefix === entityChoicesDemoTab.blockPrefix
  })

  if (!entityChoicesTabExists) {
    windowWithCoreShopDemoTabs.coreshopStudioFormDemoTabs = [...existingTabs, entityChoicesDemoTab]
  }
}
