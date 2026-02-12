/**
 * CoreShop ResourceBundle Studio Plugin
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
import {
  DynamicTypeObjectDataAbstract,
  ManyToOneRelation,
  RelationList,
  type ManyToOneRelationValue
} from '@pimcore/studio-ui-bundle/modules/element'
import { isNil } from 'lodash'

interface ClassDefinitionProps {
  assetsAllowed?: boolean
  assetTypes?: Array<{ assetTypes: string }>
  objectsAllowed?: boolean
  classes?: Array<{ classes: string }>
  documentsAllowed?: boolean
  documentTypes?: Array<{ documentTypes: string }>
}

interface DataComponentProps {
  allowedAssetTypes?: string[]
  allowedClasses?: string[]
  allowedDocumentTypes?: string[]
  allowedDataObjectTypes?: string[]
  assetsAllowed?: boolean
  documentsAllowed?: boolean
  dataObjectsAllowed?: boolean
}

function convertAllowedTypes(props: ClassDefinitionProps): DataComponentProps {
  return {
    assetsAllowed: props.assetsAllowed ?? false,
    allowedAssetTypes: props.assetTypes?.map(item => item.assetTypes) ?? [],
    dataObjectsAllowed: props.objectsAllowed ?? false,
    allowedClasses: props.classes?.map(item => item.classes) ?? [],
    // Only allow 'object' and 'variant', not 'folder'
    allowedDataObjectTypes: props.objectsAllowed ? ['object', 'variant'] : [],
    documentsAllowed: props.documentsAllowed ?? false,
    allowedDocumentTypes: props.documentTypes?.map(item => item.documentTypes) ?? []
  }
}

export class DynamicTypeObjectDataCoreShopRelation extends DynamicTypeObjectDataAbstract {
  id: string = 'coreShopRelation'
  gridCellEditMode = 'edit-modal' as const
  gridCellEditModalSettings = {
    modalSize: 'L' as const,
    formLayout: 'vertical' as const
  }

  getObjectDataComponent(props: any): React.ReactElement {
    const convertedTypes = convertAllowedTypes(props)

    return (
      <ManyToOneRelation
        {...props}
        {...convertedTypes}
        className={props.className}
        disabled={props.noteditable === true}
        inherited={props.inherited}
      />
    )
  }

  getGridCellPreviewComponent(props: any): React.ReactElement {
    const value: ManyToOneRelationValue | null = props.cellProps.getValue()
    return isNil(value) ? <></> : <RelationList relations={[value]} />
  }

  getDefaultGridColumnWidth(): number | undefined {
    return 350
  }
}
