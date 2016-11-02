<?php
namespace LuongMv\FileUploader\Block\Adminhtml\Form;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \LuongMv\FileUploader\Model\FileuploaderFactory
     */
    protected $_FileuploaderFactory;

    /**
     * @var \LuongMv\FileUploader\Model\Status
     */
    protected $_status;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \LuongMv\FileUploader\Model\FileuploaderFactory $FileuploaderFactory
     * @param \LuongMv\FileUploader\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \LuongMv\FileUploader\Model\FileuploaderFactory $FileuploaderFactory,
        \LuongMv\FileUploader\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_FileuploaderFactory = $FileuploaderFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('fileuploader_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_FileuploaderFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'fileuploader_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'fileuploader_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'name'=>'fileuploader_id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'align' => 'left',
                'name'=>'title'
            ]
        );
        $this->addColumn(
            'product_ids',
            [
                'header' => __('Products'),
                'index' => 'product_ids',
                'align' => 'left',
                'name'=>'product_ids'
            ]
        );
        $this->addColumn(
            'uploaded_file',
            [
                'header' => __('File'),
                'index' => 'uploaded_file',
                'type' => 'file',
                'align' => 'left',
                'escape' => true,
                'sortable' => false,
                'name'=>'uploaded_file',
                'renderer' => 'LuongMv\FileUploader\Block\Adminhtml\Form\Grid\Renderer\Link'
            ]
        );
        $this->addColumn(
            'content_disp',
            [
                'header' => __('Content-Disposiotion'),
                'index' => 'content_disp',
                'align' => 'left',
                'type' => 'options',
                'name'=>'content_disp',
                'options' => array(
                    0 => 'Attachment',
                    1 => 'Inline',
                ),
            ]
        );
        $this->addColumn(
            'file_status',
            [
                'header' => __('Status'),
                'index' => 'file_status',
                'align' => 'left',
                'width' => '80px',
                'type' => 'options',
                'name'=>'file_status',
                'options' => array(
                    1 => 'Enabled',
                    2 => 'Disabled',
                ),
            ]
        );
        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order',
                'align' => 'left',
                'name'=>'sort_order',
            ]
        );
       
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'fileuploader_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('fileuploader_id');
        $this->getMassactionBlock()->setTemplate('LuongMv_FileUploader::form/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('ktpl_emp');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('fileuploader/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'file_status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('fileuploader/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('fileuploader/*/grid', ['_current' => true]);
    }

//    public function getRowUrl($row)
//    {
//        return $this->getUrl(
//            'fileuploader/*/edit',
//            ['fileuploader_id' => $row->getId()]
//        );
//    }
}
