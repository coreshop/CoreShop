/**
 * CoreShop RuleBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useMemo } from 'react'
import { Card, Button, Space, Tag, Tooltip } from 'antd'
import { ArrowUpOutlined, ArrowDownOutlined, DeleteOutlined } from '@ant-design/icons'
import { container } from '@pimcore/studio-ui-bundle'
import { useTranslation } from 'react-i18next'
import type { RuleCondition } from '../types'
import type { ConditionRegistry } from '../registry/ConditionRegistry'
import { formatTypeLabel } from './type-label'

interface ConditionItemProps {
  condition: RuleCondition
  index: number
  total: number
  onChange: (condition: RuleCondition) => void
  onMove: (from: number, to: number) => void
  onDelete: () => void
  registryId: symbol | string
  currentLocale?: string
  locales?: string[]
}

export const ConditionItem: React.FC<ConditionItemProps> = ({
  condition,
  index,
  total,
  onChange,
  onMove,
  onDelete,
  registryId,
  currentLocale,
  locales
}) => {
  const handleDataChange = (configuration: Record<string, any>) => {
    onChange({ ...condition, configuration })
  }

  const conditionRegistry = useMemo(
    () => container.get<ConditionRegistry>(registryId),
    [registryId]
  )

  const ConditionComponent = conditionRegistry.get(condition.type)
  const meta = conditionRegistry.getMeta(condition.type)
  const { t } = useTranslation()

  const title = (
    <Space>
      <span style={{ fontWeight: 600 }}>
        {formatTypeLabel('Condition', condition.type)}
      </span>
      {meta !== undefined && (
        meta.indexable
          ? (
            <Tooltip title={t('coreshop_rule_condition_indexable_hint', {
              defaultValue: 'The outcome of this condition only depends on well-known context dimensions, so a precomputed index can represent it. Dimensions: {{dimensions}}',
              dimensions: (meta.dimensions ?? []).length > 0 ? (meta.dimensions ?? []).join(', ') : t('coreshop_rule_condition_indexable_none', { defaultValue: 'none' })
            })}>
              <Tag color="green">{t('coreshop_rule_condition_indexable', { defaultValue: 'Indexable' })}</Tag>
            </Tooltip>
          )
          : (
            <Tooltip title={t('coreshop_rule_condition_not_indexable_hint', {
              defaultValue: 'This condition depends on the cart or on data no index can represent. Rules containing it are skipped by precomputed indices.'
            })}>
              <Tag color="orange">{t('coreshop_rule_condition_not_indexable', { defaultValue: 'Not indexable' })}</Tag>
            </Tooltip>
          )
      )}
    </Space>
  )

  const extra = (
    <Space>
      <Button
        type="text"
        size="small"
        icon={<ArrowUpOutlined />}
        disabled={index === 0}
        onClick={() => onMove(index, index - 1)}
      />
      <Button
        type="text"
        size="small"
        icon={<ArrowDownOutlined />}
        disabled={index === total - 1}
        onClick={() => onMove(index, index + 1)}
      />
      <Button
        type="text"
        size="small"
        icon={<DeleteOutlined />}
        danger
        onClick={onDelete}
      />
    </Space>
  )

  return (
    <Card
      size="small"
      title={title}
      extra={extra}
      style={{ marginBottom: 8 }}
    >
      {ConditionComponent ? (
        <ConditionComponent
          data={condition.configuration}
          type={condition.type}
          onChange={handleDataChange}
          registryId={registryId}
          currentLocale={currentLocale}
          locales={locales}
        />
      ) : (
        <div style={{ color: '#999', fontStyle: 'italic' }}>
          Unknown condition type: {condition.type}
        </div>
      )}
    </Card>
  )
}
