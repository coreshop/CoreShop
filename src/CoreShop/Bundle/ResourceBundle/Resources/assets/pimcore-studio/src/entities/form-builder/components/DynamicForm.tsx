/**
 * CoreShop Form Builder - Dynamic Form Component
 *
 * Renders a form based on FormBuilderConfig.
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
import { Form, Collapse, Row, Col } from 'antd'
import type { FormInstance } from 'antd/es/form'
import type { FormBuilderConfig, FieldDefinition, SectionDefinition } from '../types'
import { useTranslation } from 'react-i18next'

export interface DynamicFormProps<T = any> {
  /** Form configuration from builder */
  config: FormBuilderConfig<T>
  /** Form data */
  data?: T
  /** Change handler */
  onChange: (draft: Partial<T>) => void
  /** Current locale */
  currentLocale?: string
  /** Form instance (optional, for external control) */
  form?: FormInstance
}

/**
 * DynamicForm
 *
 * Renders a form based on FormBuilderConfig from FormBuilder.
 *
 * @example
 * ```typescript
 * const config = builder.build({ data })
 *
 * <DynamicForm
 *   config={config}
 *   data={data}
 *   onChange={(draft) => setData(prev => ({ ...prev, ...draft }))}
 * />
 * ```
 */
export const DynamicForm = <T extends Record<string, any> = any>({
  config,
  data,
  onChange,
  currentLocale,
  form: externalForm
}: DynamicFormProps<T>): React.JSX.Element => {
  const [internalForm] = Form.useForm()
  const form = externalForm ?? internalForm
  const { t } = useTranslation()

  // Update form values when data or locale changes
  // This is critical for localized fields to work correctly
  React.useEffect(() => {
    if (data) {
      form.setFieldsValue(data)
    }
  }, [data, currentLocale, form])

  // Group fields by section
  const fieldsBySection = React.useMemo(() => {
    const grouped = new Map<string | undefined, FieldDefinition<T>[]>()

    for (const field of config.fields) {
      const section = field.section
      const fields = grouped.get(section) ?? []
      fields.push(field)
      grouped.set(section, fields)
    }

    return grouped
  }, [config.fields])

  // Sections sorted by order
  const sortedSections = React.useMemo(() => {
    if (!config.sections) return []
    return [...config.sections].sort((a, b) => {
      const orderA = a.order ?? 999
      const orderB = b.order ?? 999
      return orderA - orderB
    })
  }, [config.sections])

  // Render a single field
  const renderField = (field: FieldDefinition<T>) => {
    const FieldComponent = field.component

    // Translate label if it's a translation key
    let label = field.label ? t(field.label, { defaultValue: field.label }) : undefined

    // If field is localized, append current locale to label
    if (field.localized && currentLocale && label) {
      label = `${label} (${currentLocale.toUpperCase()})`
    }

    // Translate tooltip if provided
    const tooltip = field.tooltip ? t(field.tooltip, { defaultValue: field.tooltip }) : undefined

    const formItem = (
      <Form.Item
        key={field.name}
        label={label}
        name={field.name}
        rules={field.rules}
        required={field.required}
        tooltip={tooltip}
        hidden={field.hidden}
      >
        <FieldComponent
          disabled={field.disabled}
          {...(field.componentProps ?? {})}
        />
      </Form.Item>
    )

    // Apply wrapper if provided
    if (field.wrapper) {
      return field.wrapper(formItem)
    }

    // Apply grid span if provided
    if (field.span) {
      return (
        <Col key={field.name} span={field.span}>
          {formItem}
        </Col>
      )
    }

    return formItem
  }

  // Render fields for a section
  const renderSectionFields = (sectionKey?: string) => {
    const fields = fieldsBySection.get(sectionKey) ?? []
    if (fields.length === 0) return null

    // If columns specified, use Row/Col layout
    if (config.columns && config.columns > 1) {
      return (
        <Row gutter={16}>
          {fields.map(field => renderField(field))}
        </Row>
      )
    }

    return fields.map(field => renderField(field))
  }

  // Render a section
  const renderSection = (section: SectionDefinition) => {
    const content = renderSectionFields(section.key)
    if (!content) return null

    // Translate section title and description
    const title = t(section.title, { defaultValue: section.title })
    const description = section.description ? t(section.description, { defaultValue: section.description }) : undefined

    if (section.collapsible) {
      return (
        <Collapse
          key={section.key}
          defaultActiveKey={section.defaultCollapsed ? [] : [section.key]}
          style={{ marginBottom: 16 }}
        >
          <Collapse.Panel
            header={
              <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                {section.icon}
                <span>{title}</span>
              </div>
            }
            key={section.key}
          >
            {description && (
              <div style={{ marginBottom: 16, color: 'var(--ant-color-text-secondary)' }}>
                {description}
              </div>
            )}
            {content}
          </Collapse.Panel>
        </Collapse>
      )
    }

    return (
      <div key={section.key} style={{ marginBottom: 24 }}>
        <h3 style={{ marginBottom: 16 }}>
          {section.icon} {title}
        </h3>
        {description && (
          <div style={{ marginBottom: 16, color: 'var(--ant-color-text-secondary)' }}>
            {description}
          </div>
        )}
        {content}
      </div>
    )
  }

  return (
    <Form
      form={form}
      layout={config.layout ?? 'vertical'}
      initialValues={data}
      onValuesChange={(changedValues) => {
        onChange(changedValues as Partial<T>)
      }}
      {...(config.formProps ?? {})}
    >
      {/* Render sections */}
      {sortedSections.map(section => renderSection(section))}

      {/* Render fields without section */}
      {renderSectionFields(undefined)}
    </Form>
  )
}
