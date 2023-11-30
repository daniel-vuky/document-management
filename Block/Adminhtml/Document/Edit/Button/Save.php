<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Block\Adminhtml\Document\Edit\Button;

use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 */
class Save extends Generic
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed('Anhvdk_DocumentManagement::edit_document_management')) {
            return [];
        }
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'document_form.document_form',
                                'actionName' => 'save',
                                'params' => [
                                    false
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_continue',
            'label' => __('Save & Continue'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'document_form.document_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'stay' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
