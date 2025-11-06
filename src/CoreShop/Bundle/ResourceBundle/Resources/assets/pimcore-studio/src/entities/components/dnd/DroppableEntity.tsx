import React from 'react'
import { Droppable, type DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'

export interface DroppableEntityProps {
  accept: string | string[]
  onDrop: (info: DragAndDropInfo) => void
  className?: string
  disabled?: boolean
  isValidData?: (info: DragAndDropInfo) => boolean
  children: React.ReactNode
}

export const DroppableEntity: React.FC<DroppableEntityProps> = ({ accept, onDrop, className, disabled, isValidData, children }) => {
  const accepts = Array.isArray(accept) ? accept : [accept]
  return (
    <Droppable
      className={ className }
      disabled={ disabled }
      isValidContext={ function (info) { return accepts.includes(info.type);} }
      isValidData={ (info) => isValidData ? isValidData(info) : true }
      onDrop={ onDrop }
    >
      {children}
    </Droppable>
  )
}
