<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model;

use Exception;
use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Anhvdk\DocumentManagement\Api\Data\DocumentSearchResultInterface;
use Anhvdk\DocumentManagement\Api\Data\DocumentSearchResultInterfaceFactory;
use Anhvdk\DocumentManagement\Api\DocumentRepositoryInterface;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document as DocumentResource;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document\Collection;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document\CollectionFactory as DocumentCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Document Repository
 */
class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * Document Resource Model
     *
     * @var DocumentResource
     */
    protected DocumentResource $documentResource;

    /**
     * Document Factory
     *
     * @var DocumentFactory
     */
    protected DocumentFactory $documentFactory;

    /**
     * Collection Processor
     *
     * @var CollectionProcessorInterface
     */
    protected CollectionProcessorInterface $collectionProcessor;

    /**
     * Document Collection Factory
     *
     * @var DocumentCollectionFactory
     */
    protected DocumentCollectionFactory $documentCollectionFactory;

    /**
     * Document Search Result Interface Factory
     *
     * @var DocumentSearchResultInterfaceFactory
     */
    protected DocumentSearchResultInterfaceFactory $documentSearchResultInterfaceFactory;

    /**
     * Document Repository Constructor
     *
     * @param DocumentResource $documentResource
     * @param DocumentFactory $documentFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DocumentCollectionFactory $documentCollectionFactory
     * @param DocumentSearchResultInterfaceFactory $documentSearchResultInterfaceFactory
     */
    public function __construct(
        DocumentResource $documentResource,
        DocumentFactory $documentFactory,
        CollectionProcessorInterface $collectionProcessor,
        DocumentCollectionFactory $documentCollectionFactory,
        DocumentSearchResultInterfaceFactory $documentSearchResultInterfaceFactory
    ) {
        $this->documentResource = $documentResource;
        $this->documentFactory = $documentFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->documentCollectionFactory = $documentCollectionFactory;
        $this->documentSearchResultInterfaceFactory = $documentSearchResultInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(DocumentInterface $document)
    {
        try {
            $this->documentResource->save($document);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'The document was unable to be saved. %1',
                    $exception->getMessage()
                )
            );
        }

        return $document;
    }

    /**
     * @inheritDoc
     */
    public function get(int $documentId)
    {
        $document = $this->documentFactory->create();
        $this->documentResource->load($document, $documentId);
        if (!$document->getId()) {
            throw new NoSuchEntityException(
                __(
                    'The document with the "%1" ID wasn\'t found. Verify the ID and try again.',
                    $documentId
                )
            );
        }

        return $document;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->documentCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var DocumentSearchResultInterface $searchResults */
        $searchResults = $this->documentSearchResultInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(DocumentInterface $document)
    {
        try {
            $this->documentResource->delete($document);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove document with id "%1"',
                    $document->getEntityId()
                ),
                $exception
            );
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $documentId)
    {
        try {
            $document = $this->get($documentId);
            $this->delete($document);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove document with id "%1"',
                    $documentId
                ),
                $exception
            );
        }

        return true;
    }
}
