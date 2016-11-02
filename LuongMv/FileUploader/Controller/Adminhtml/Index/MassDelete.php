<?php
namespace LuongMv\FileUploader\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $empIds = $this->getRequest()->getParam('ktpl_emp');
        if (!is_array($empIds) || empty($empIds)) {
            $this->messageManager->addError(__('Please select Fileuploader.'));
        } else {
            try {
                foreach ($empIds as $empId) {
                    $emp = $this->_objectManager->get('LuongMv\FileUploader\Model\Fileuploader')->load($empId);
                    $emp->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($empIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('fileuploader/*/index');
    }
}
