<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Anhvdk\DocumentManagement\Model\Document\Source\Website as WebsiteOptions;

/**
 * Document Listing Column Website
 */
class Websites extends Column
{
    /**
     * Website Options
     *
     * @var WebsiteOptions
     */
    protected WebsiteOptions $websiteOptions;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param WebsiteOptions $websiteOptions
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        WebsiteOptions $websiteOptions,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->websiteOptions = $websiteOptions;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $websiteNames = [];
        foreach ($this->websiteOptions->toOptionArray() as $website) {
            $websiteNames[$website['value']] = $website['label'];
        }
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $websites = [];
                $websiteIds = explode(',', $item[$fieldName]);
                foreach ($websiteIds as $websiteId) {
                    if (!isset($websiteNames[$websiteId])) {
                        continue;
                    }
                    $websites[] = $websiteNames[$websiteId];
                }
                $item[$fieldName] = $this->addScrollToField(implode('</br>', $websites));
            }
        }

        return $dataSource;
    }

    /**
     * @param string $html
     * @return string
     */
    public function addScrollToField($html)
    {
        return '<div style="max-height: 85px;overflow-y: auto">'
            . $html
            . '</div>';
    }
}
