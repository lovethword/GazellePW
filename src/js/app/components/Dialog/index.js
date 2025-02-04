import renderElement from '#/app/utils/renderElement'
import Dialog from './Dialog'
import useScrollLock from './useScrollLock'

Dialog.open = renderElement
Dialog.useScrollLock = useScrollLock

export default Dialog
