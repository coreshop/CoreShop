/**
 * CoreShop Customer Company Assignment Module
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

// Transient launcher widgets (open Element Selector, close themselves)
export { AssignToNewCompanyLauncher } from './AssignToNewCompanyLauncher'
export { AssignToExistingCompanyLauncher } from './AssignToExistingCompanyLauncher'

// Persistent detail panel widgets (receive IDs from widget config)
export { AssignToNewCompanyPanel } from './AssignToNewCompanyPanel'
export { AssignToExistingCompanyPanel } from './AssignToExistingCompanyPanel'

// Widget restorer for browser reload persistence
export { customerCompanyAssignmentWidgetRestorer } from './CustomerCompanyAssignmentWidgetRestorer'

// Modal components
export { AssignToNewCompanyModal } from './AssignToNewCompanyModal'
export { AssignToExistingCompanyModal } from './AssignToExistingCompanyModal'

// Shared components
export { AssignmentForm } from './AssignmentForm'
export { customerCompanyApi } from './api'
