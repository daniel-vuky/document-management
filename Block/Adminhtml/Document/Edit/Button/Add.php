<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Block\Adminhtml\Document\Edit\Button;

/**
 * Button Add Class
 */
class Add extends Generic
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed('Anhvdk_DocumentManagement::edit_document_management')) {
            return [];
        }

        return [
            'label' => __('Add New Document'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/new')),
            'class' => 'primary',
            'sort_order' => 10
        ];
    }
}
