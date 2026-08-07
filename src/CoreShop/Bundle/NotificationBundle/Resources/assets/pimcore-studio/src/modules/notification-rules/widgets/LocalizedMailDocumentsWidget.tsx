/**
 * CoreShop NotificationBundle Studio Plugin
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
import { Tabs } from 'antd'
import { DocumentSelect } from '@coreshop/pimcore/src/components/DocumentSelect'
import { useStudioLanguages } from '@coreshop/resource/src/components/localization/useStudioLanguages'

interface LocalizedMailDocumentsWidgetProps {
  value?: Record<string, number | null>
  onChange?: (value: Record<string, number | null>) => void
}

export const LocalizedMailDocumentsWidget: React.FC<LocalizedMailDocumentsWidgetProps> = ({
  value,
  onChange,
}) => {
  const languages = useStudioLanguages()
  const mails = value ?? {}

  const handleMailChange = (lang: string, documentId: number | null) => {
    onChange?.({
      ...mails,
      [lang]: documentId,
    })
  }

  const tabItems = languages.map(lang => ({
    key: lang,
    label: lang.toUpperCase(),
    children: (
      <div style={{ padding: 8 }}>
        <DocumentSelect
          value={mails[lang] ?? null}
          onChange={(id) => handleMailChange(lang, id)}
          documentTypes={['email']}
        />
      </div>
    ),
  }))

  return (
    <Tabs
      defaultActiveKey={languages[0]}
      items={tabItems}
      size="small"
    />
  )
}
