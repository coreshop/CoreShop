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
import { Form, Collapse, Row, Col, Tag } from 'antd'
import { GlobalOutlined } from '@ant-design/icons'
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

  // Track entity ID and locale to only reset form when they change
  const lastLoadedIdRef = React.useRef<number | undefined>(undefined)
  const lastLoadedLocaleRef = React.useRef<string | undefined>(undefined)

  // Check if config has localized fields
  const hasLocalizedFields = React.useMemo(() =>
    config.fields.some(f => f.localized),
    [config.fields]
  )

  // Update form values when data or locale changes
  // This is critical for localized fields to work correctly
  React.useEffect(() => {
    const entityId = (data as any)?.id
    const entityChanged = lastLoadedIdRef.current !== entityId
    const localeChanged = lastLoadedLocaleRef.current !== currentLocale

    // Only reset form when switching entities or locales
    if (!entityChanged && !localeChanged) {
      return
    }

    lastLoadedIdRef.current = entityId
    lastLoadedLocaleRef.current = currentLocale

    if (data) {
      // Ensure translations structure exists for current locale
      const formData: any = { ...data }
      if (hasLocalizedFields && currentLocale) {
        formData.translations = formData.translations ?? {}
        if (!formData.translations[currentLocale]) {
          formData.translations[currentLocale] = {}
        }
      }
      form.setFieldsValue(formData)
    }
  }, [(data as any)?.id, currentLocale, form, hasLocalizedFields])

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

    // For localized fields, show a visual indicator and the locale
    let localizedLabel: React.ReactNode = label
    if (field.localized && currentLocale) {
      localizedLabel = (
        <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
          <GlobalOutlined style={{ color: 'var(--ant-color-primary)', fontSize: 12 }} />
          {label}
          <Tag color="blue" style={{ marginLeft: 4, fontSize: 10, lineHeight: '16px', padding: '0 4px' }}>
            {currentLocale.toUpperCase()}
          </Tag>
        </span>
      )
    }

    // Translate tooltip if provided
    const tooltip = field.tooltip ? t(field.tooltip, { defaultValue: field.tooltip }) : undefined

    // For localized fields, use translations[currentLocale][fieldName] path
    const fieldName = field.localized && currentLocale
      ? ['translations', currentLocale, ...(Array.isArray(field.name) ? field.name : [field.name])]
      : field.name

    const formItem = (
      <Form.Item
        key={String(fieldName)}
        label={localizedLabel}
        name={fieldName}
        rules={field.rules}
        required={field.required}
        tooltip={tooltip}
        hidden={field.hidden}
        valuePropName={field.valuePropName}
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
        <Col key={String(fieldName)} span={field.span}>
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
