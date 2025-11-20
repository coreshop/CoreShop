import React, { useMemo, useState, useEffect } from 'react'
import { Form } from 'antd'
import { useTranslation } from 'react-i18next'
import { ManyToManyRelation } from '@pimcore/studio-ui-bundle/modules/element'
import type { ManyToManyRelationValue } from '@coreshop/resource/src/entities/types/relation'
import { container } from '@pimcore/studio-ui-bundle'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

export interface CategoryMultiSelectProps {
    name?: string
    label?: string
    labelKey?: string
    value?: string[] | ManyToManyRelationValue
    onChange?: (value: string[]) => void
}

export const CategoryMultiSelect: React.FC<CategoryMultiSelectProps> = ({
    name = 'categories',
    label,
    labelKey,
    value,
    onChange
}) => {
    const { t } = useTranslation()
    const [allowedClasses, setAllowedClasses] = useState<string[]>([])
    const [relationValue, handleRelationChange] = useRelationIds(value, 'Category')

    const configProvider = useMemo(
        () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
        []
    )

    useEffect(() => {
        const loadAllowedClasses = async () => {
            const classes = await configProvider.getAllowedClasses('coreshop.category')
            setAllowedClasses(classes)
        }
        loadAllowedClasses()
    }, [configProvider])

    const handleChange = (val: ManyToManyRelationValue | null) => {
        const ids = handleRelationChange(val)
        if (onChange) {
            onChange(ids)
        }
    }

    const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_categories', { defaultValue: 'Categories' }))

    return (
        <Form.Item label={computedLabel} name={name}>
            <ManyToManyRelation
                allowedClasses={allowedClasses}
                dataObjectsAllowed={true}
                assetsAllowed={false}
                documentsAllowed={false}
                allowToClearRelation={false}
                maxItems={null}
                pathFormatterClass={null}
                width={null}
                height={null}
                value={relationValue}
                onChange={handleChange}
            />
        </Form.Item>
    )
}
