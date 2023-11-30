<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model\Document\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\ResourceModel\Website\Collection as WebsiteCollection;

/**
 * Class Website
 */
class Website implements OptionSourceInterface
{
    /**
     * Website Collection
     *
     * @var WebsiteCollection
     */
    protected WebsiteCollection $websiteCollection;

    /**
     * @param WebsiteCollection $websiteCollection
     */
    public function __construct(
        WebsiteCollection $websiteCollection
    ) {
        $this->websiteCollection = $websiteCollection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return $this->websiteCollection->toOptionArray();
    }
}
