import React, { useMemo, useState, useEffect, Component, type ErrorInfo } from 'react'
import { Form, Alert, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import { ManyToManyRelation } from '@pimcore/studio-ui-bundle/modules/element'
import type { ManyToManyRelationValue } from '@coreshop/resource/src/entities/types/relation'
import { container } from '@pimcore/studio-ui-bundle'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

// Error boundary to catch ManyToManyRelation errors
interface ErrorBoundaryState {
    hasError: boolean
    error?: Error
}

class ManyToManyRelationErrorBoundary extends Component<{ children: React.ReactNode }, ErrorBoundaryState> {
    constructor(props: { children: React.ReactNode }) {
        super(props)
        this.state = { hasError: false }
    }

    static getDerivedStateFromError(error: Error): ErrorBoundaryState {
        console.error('[ManyToManyRelationErrorBoundary] Caught error:', error)
        return { hasError: true, error }
    }

    componentDidCatch(error: Error, errorInfo: ErrorInfo): void {
        console.error('[ManyToManyRelationErrorBoundary] Error details:', error, errorInfo)
    }

    render(): React.ReactNode {
        if (this.state.hasError) {
            return (
                <Alert
                    type="error"
                    message="Error loading relation component"
                    description={this.state.error?.message}
                />
            )
        }
        return this.props.children
    }
}

export interface ProductMultiSelectProps {
    name?: string
    label?: string
    labelKey?: string
    value?: string[] | ManyToManyRelationValue
    onChange?: (value: string[]) => void
}

export const ProductMultiSelect: React.FC<ProductMultiSelectProps> = ({
    name = 'products',
    label,
    labelKey,
    value,
    onChange
}) => {
    const { t } = useTranslation()
    const [allowedClasses, setAllowedClasses] = useState<string[]>([])
    const [relationValue, handleRelationChange, loading] = useRelationIds(value, 'Product')

    const configProvider = useMemo(
        () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
        []
    )

    useEffect(() => {
        const loadAllowedClasses = async () => {
            const classes = await configProvider.getAllowedClasses('coreshop.product')
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

    const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_report_products', { defaultValue: 'Products' }))

    return (
        <Form.Item label={computedLabel} name={name}>
            <Spin spinning={loading}>
                <ManyToManyRelationErrorBoundary>
                    <ManyToManyRelation
                        allowedClasses={allowedClasses}
                        dataObjectsAllowed={true}
                        allowedDataObjectTypes={['object']}
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
                </ManyToManyRelationErrorBoundary>
            </Spin>
        </Form.Item>
    )
}
