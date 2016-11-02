<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace LuongMv\FileUploader\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class PostActions
 */
class PostActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \LuongMv\FileUploader\Helper\Data
     */
    protected $helper;

    /**
     * PostActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \LuongMv\FileUploader\Helper\Data $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \LuongMv\FileUploader\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'fileuploader/index/edit',
                        ['fileuploader_id' => $item['fileuploader_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
                $d = ($item['content_disp'] =='Attachment') ? '0' : '1';
                $url = $this->helper->getDownloadFileUrl($item['uploaded_file'], $d);
                if (!empty($item['uploaded_file'])) {
                    $item[$this->getData('name')]['download'] = [
                        'href' => $url,
                        'label' => __('Download'),
                        'hidden' => false,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
