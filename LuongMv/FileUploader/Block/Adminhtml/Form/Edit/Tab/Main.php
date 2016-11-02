<?php

namespace LuongMv\FileUploader\Block\Adminhtml\Form\Edit\Tab;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \LuongMv\FileUploader\Model\Status
     */
    protected $_status;

    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \LuongMv\FileUploader\Model\Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \LuongMv\FileUploader\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_formFactory = $formFactory;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('form_post');
       

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        $fieldset->addType(
            'fileuploader',
            '\LuongMv\FileUploader\Block\Adminhtml\Customformfield\Edit\Renderer\CustomRenderer'
        );


        if ($model->getId()) {
            $fieldset->addField('fileuploader_id', 'hidden', ['name' => 'fileuploader_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'uploaded_file',
            'fileuploader',
            [
                'label' => __('File'),
                'title' => __('File'),
                'name' => 'uploaded_file',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'file_status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'file_status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => __('Enabled'),
                    ),
                    array(
                        'value' => 2,
                        'label' => __('Disabled'),
                    ),
                ),
            ]
        );

        $fieldset->addField(
            'content_disp',
            'select',
            [
                'label' => __('Content-Disposition'),
                'title' => __('Content-Disposition'),
                'name' => 'content_disp',
                'values' => array(
                    array(
                        'value' => 0,
                        'label' => __('Attachment'),
                    ),
                    array(
                        'value' => 1,
                        'label' => __('Inline'),
                    ),
                ),
            ]
        );

        $fieldset->addField(
            'file_content',
            'editor',
            [
                'name' => 'file_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'required' => false,
                'style' => 'width:600px; height:200px;',
                'wysiwyg' => false,
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'name' => 'sort_order',
                'required' => false,
            ]
        );

        if ($model->getData('uploaded_file')) {
            $model->setData('uploaded_file', $model->getData('uploaded_file'));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('File Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('File Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
