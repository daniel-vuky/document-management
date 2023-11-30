<?php

namespace Anhvdk\DocumentManagement\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Document Entity Search Result Interface
 */
interface DocumentSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Anhvdk\DocumentManagement\Api\Data\DocumentInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Anhvdk\DocumentManagement\Api\Data\DocumentInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
