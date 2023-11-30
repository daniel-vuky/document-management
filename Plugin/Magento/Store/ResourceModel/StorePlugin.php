<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Plugin\Magento\Store\ResourceModel;

use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Anhvdk\DocumentManagement\Model\DocumentUrlRewriteGenerator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ResourceModel\Store as ResourceStore;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document\CollectionFactory as DocumentCollection;

/**
 * Plugin of magento store resource model
 */
class StorePlugin
{
    /**
     * @var UrlPersistInterface
     */
    private UrlPersistInterface $urlPersist;

    /**
     * @var DocumentUrlRewriteGenerator
     */
    private DocumentUrlRewriteGenerator $documentUrlRewriteGenerator;

    /**
     * @var DocumentCollection
     */
    protected DocumentCollection $documentCollection;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param DocumentUrlRewriteGenerator $documentUrlRewriteGenerator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        DocumentCollection $documentCollection,
        DocumentUrlRewriteGenerator $documentUrlRewriteGenerator,
        StoreManagerInterface $storeManager
    ) {
        $this->urlPersist = $urlPersist;
        $this->documentCollection = $documentCollection;
        $this->documentUrlRewriteGenerator = $documentUrlRewriteGenerator;
        $this->storeManager = $storeManager;
    }

    /**
     * Replace content url rewrites on store view save
     *
     * @param ResourceStore $object
     * @param ResourceStore $result
     * @param AbstractModel $store
     *
     * @return ResourceStore
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    public function afterSave(
        ResourceStore $object,
        ResourceStore $result,
        AbstractModel $store
    ): ResourceStore {
        if ($store->dataHasChangedFor('website_id')) {
            $storeId = $store->getId();
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $this->urlPersist->replace(
                $this->generateDocumentUrl($websiteId)
            );
        }

        return $result;
    }

    /**
     * Generate url rewrites for document to store view
     *
     * @param int $websiteId
     *
     * @return array
     */
    private function generateDocumentUrl(int $websiteId)
    {
        $rewrites = [];
        $urls = [];
        foreach ($this->getDocumentItems($websiteId) as $document) {
            $rewrites[] = $this->documentUrlRewriteGenerator->generate($document);
        }

        return array_merge($urls, ...$rewrites);
    }

    /**
     * Return document items for all store view
     *
     * @param int $websiteId
     *
     * @return DocumentInterface[]
     */
    private function getDocumentItems(int $websiteId): array
    {
        $collection = $this->documentCollection->create();
        $collection->addFieldToFilter(
            DocumentInterface::WEBSITE_IDS,
            [
                'finset' => $websiteId
            ]
        );

        return $collection->getItems();
    }
}
