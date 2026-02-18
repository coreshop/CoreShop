/**
 * CoreShop StudioFormBundle - Demo Showcase Widget
 *
 * Interactive demo of all supported form types rendered via SchemaForm.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Card, Tabs, Collapse, Typography } from 'antd'
import { SchemaForm } from '../../schema-adapter/SchemaForm'

const { Text } = Typography

// --- PHP source code for each demo ---

const BASIC_PHP = `final class BasicDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Active',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_basic';
    }
}`

const CHOICES_PHP = `final class ChoiceDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status (Select)',
                'choices' => [
                    'Draft' => 'draft',
                    'Published' => 'published',
                    'Archived' => 'archived',
                ],
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags (Multi-Select)',
                'choices' => [
                    'Featured' => 'featured',
                    'Sale' => 'sale',
                    'New' => 'new',
                    'Bestseller' => 'bestseller',
                ],
                'multiple' => true,
            ])
            ->add('visibility', ChoiceType::class, [
                'label' => 'Visibility (Radio)',
                'choices' => [
                    'Public' => 'public',
                    'Private' => 'private',
                    'Unlisted' => 'unlisted',
                ],
                'expanded' => true,
            ])
            ->add('channels', ChoiceType::class, [
                'label' => 'Channels (Checkbox Group)',
                'choices' => [
                    'Web' => 'web',
                    'Mobile' => 'mobile',
                    'POS' => 'pos',
                    'API' => 'api',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_choices';
    }
}`

const FIELD_TYPES_PHP = `final class FieldTypesDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('url', UrlType::class, ['label' => 'URL'])
            ->add('password', PasswordType::class, ['label' => 'Password'])
            ->add('number', NumberType::class, ['label' => 'Number (scale=2)', 'scale' => 2])
            ->add('integer', IntegerType::class, ['label' => 'Integer'])
            ->add('date', DateType::class, ['label' => 'Date', 'widget' => 'single_text'])
            ->add('datetime', DateTimeType::class, ['label' => 'DateTime', 'widget' => 'single_text'])
            ->add('time', TimeType::class, ['label' => 'Time', 'widget' => 'single_text'])
            ->add('color', ColorType::class, ['label' => 'Color'])
            ->add('range', RangeType::class, ['label' => 'Range'])
        ;
    }
}

// Schema Enricher groups fields into collapsible sections:
final class FieldTypesDemoSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_SECTIONS = [
        'email' => 'text_inputs', 'url' => 'text_inputs', 'password' => 'text_inputs',
        'number' => 'numeric_date', 'integer' => 'numeric_date',
        'date' => 'numeric_date', 'datetime' => 'numeric_date', 'time' => 'numeric_date',
        'color' => 'numeric_date', 'range' => 'numeric_date',
    ];

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->sections = [
            new SectionSchema('text_inputs', 'Text Inputs', 100, true, false),
            new SectionSchema('numeric_date', 'Numeric & Date', 90, true, true),
        ];

        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}`

const COLLECTION_PHP = `final class CollectionEntryDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('active', CheckboxType::class, ['label' => 'Active'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_collection_entry';
    }
}

final class CollectionDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupName', TextType::class, [
                'label' => 'Group Name',
            ])
            ->add('members', CollectionType::class, [
                'label' => 'Members',
                'entry_type' => CollectionEntryDemoType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_collection';
    }
}`

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

// --- Components ---

interface DemoTabProps {
  blockPrefix: string
  description: string
  phpSource: string
}

const DemoTab: React.FC<DemoTabProps> = ({ blockPrefix, description, phpSource }) => {
  const [data, setData] = React.useState<Record<string, any>>({})

  const handleChange = React.useCallback((draft: Record<string, any>) => {
    setData((prev) => ({ ...prev, ...draft }))
  }, [])

  return (
    <div>
      <Text type="secondary" style={{ display: 'block', marginBottom: 16 }}>
        {description}
      </Text>

      <SchemaForm
        blockPrefix={blockPrefix}
        data={data}
        onChange={handleChange}
      />

      <Collapse
        style={{ marginTop: 16 }}
        items={[
          {
            key: 'php',
            label: 'PHP Source',
            children: (
              <pre style={{ margin: 0, fontSize: 12, maxHeight: 400, overflow: 'auto', background: '#f5f5f5', padding: 12, borderRadius: 4 }}>
                {phpSource}
              </pre>
            ),
          },
          {
            key: 'state',
            label: 'Current State (JSON)',
            children: (
              <pre style={{ margin: 0, fontSize: 12, maxHeight: 300, overflow: 'auto' }}>
                {JSON.stringify(data, null, 2)}
              </pre>
            ),
          },
        ]}
      />
    </div>
  )
}

export const DemoShowcase: React.FC = () => {
  const tabs = [
    {
      key: 'basic',
      label: 'Basic',
      children: (
        <DemoTab
          blockPrefix="coreshop_demo_basic"
          description="Minimal form: TextType, CheckboxType, TextareaType."
          phpSource={BASIC_PHP}
        />
      ),
    },
    {
      key: 'choices',
      label: 'Choices',
      children: (
        <DemoTab
          blockPrefix="coreshop_demo_choices"
          description="All 4 ChoiceType variants: Select, Multi-Select, Radio.Group, Checkbox.Group."
          phpSource={CHOICES_PHP}
        />
      ),
    },
    {
      key: 'field-types',
      label: 'Field Types',
      children: (
        <DemoTab
          blockPrefix="coreshop_demo_field_types"
          description="All supported field types with collapsible sections: Email, URL, Password, Number, Integer, Date, DateTime, Time, Color, Range."
          phpSource={FIELD_TYPES_PHP}
        />
      ),
    },
    {
      key: 'entity-choices',
      label: 'Entity Choices',
      children: (
        <DemoTab
          blockPrefix="coreshop_demo_entity_choices"
          description="CoreShop entity ChoiceTypes rendered via EntityChoiceWidget: Country, State, Zone, Currency, Store, Payment Provider, Tax Rate, Tax Rule Group — with single and multi-select variants."
          phpSource={ENTITY_CHOICES_PHP}
        />
      ),
    },
    {
      key: 'collection',
      label: 'Collection',
      children: (
        <DemoTab
          blockPrefix="coreshop_demo_collection"
          description="Dynamic list with compound sub-forms: each entry has Name, Email, Active fields. Add/remove items dynamically."
          phpSource={COLLECTION_PHP}
        />
      ),
    },
  ]

  return (
    <Card title="StudioFormBundle Demos" style={{ margin: 16 }}>
      <Tabs items={tabs} />
    </Card>
  )
}
