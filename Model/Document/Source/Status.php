<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model\Document\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    const DRAFT = 1;
    const PUBLISHED = 2;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DRAFT, 'label' => __('Draft')],
            ['value' => self::PUBLISHED, 'label' => __('Published')]
        ];
    }
}
