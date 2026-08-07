import React from 'react'
import { Toolbar as PimToolbar, IconButton, Button, Popconfirm } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'

export interface EntityFooterToolbarProps {
  dirty?: boolean
  loading?: boolean
  onReload?: () => void
  onSave?: () => void
  leftExtras?: React.ReactNode
}

export const EntityFooterToolbar: React.FC<EntityFooterToolbarProps> = ({ dirty, loading, onReload, onSave, leftExtras }) => {
  const { t } = useTranslation()

  return (
    <PimToolbar>
      {dirty
        ? (
          <Popconfirm
            title={ t('toolbar.reload.confirmation', { defaultValue: 'Discard changes and reload?' }) }
            onConfirm={ onReload }
          >
            <IconButton icon={ { value: 'refresh' } }>
              {t('toolbar.reload', { defaultValue: 'Reload' })}
            </IconButton>
          </Popconfirm>
          )
        : (
          <IconButton icon={ { value: 'refresh' } } onClick={ onReload }>
            {t('toolbar.reload', { defaultValue: 'Reload' })}
          </IconButton>
          )}

      {leftExtras}

      <Button
        disabled={ !dirty || loading }
        loading={ loading }
        onClick={ onSave }
        type='primary'
      >
        {t('toolbar.save-and-publish', { defaultValue: 'Save & Publish' })}
      </Button>
    </PimToolbar>
  )
}
