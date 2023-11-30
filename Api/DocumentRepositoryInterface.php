<?php

namespace Anhvdk\DocumentManagement\Api;

use Anhvdk\DocumentManagement\Api\Data\DocumentSearchResultInterface;

/**
 * Document Repository Interface
 */
interface DocumentRepositoryInterface
{
    /**
     * Save Document
     *
     * @param \Anhvdk\DocumentManagement\Api\Data\DocumentInterface $document
     *
     * @return \Anhvdk\DocumentManagement\Api\Data\DocumentInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Anhvdk\DocumentManagement\Api\Data\DocumentInterface $document);

    /**
     * Get Document By ID
     *
     * @param int $documentId
     *
     * @return \Anhvdk\DocumentManagement\Api\Data\DocumentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $documentId);

    /**
     * Load document data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Anhvdk\DocumentManagement\Api\Data\DocumentSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete document
     *
     * @param \Anhvdk\DocumentManagement\Api\Data\DocumentInterface $document
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Anhvdk\DocumentManagement\Api\Data\DocumentInterface $document);

    /**
     * Delete document by ID
     *
     * @param int $documentId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $documentId);
}
