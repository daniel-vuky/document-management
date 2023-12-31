<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model;

use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

/**
 * Document Url Rewrite Generator
 */
class DocumentUrlRewriteGenerator
{
    /**
     * Entity type code
     */
    const ENTITY_TYPE = 'Anhvdk-document';

    /**
     * @var UrlRewriteFactory
     */
    protected UrlRewriteFactory $urlRewriteFactory;

    /**
     * @var DocumentUrlPathGenerator
     */
    protected DocumentUrlPathGenerator $documentUrlPathGenerator;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var StoreWebsiteRelationInterface
     */
    protected StoreWebsiteRelationInterface $storeWebsiteRelation;

    /**
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param DocumentUrlPathGenerator $documentUrlPathGenerator
     * @param StoreManagerInterface $storeManager
     * @param StoreWebsiteRelationInterface $storeWebsiteRelation
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        DocumentUrlPathGenerator $documentUrlPathGenerator,
        StoreManagerInterface $storeManager,
        StoreWebsiteRelationInterface $storeWebsiteRelation
    ) {
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->storeManager = $storeManager;
        $this->documentUrlPathGenerator = $documentUrlPathGenerator;
        $this->storeWebsiteRelation = $storeWebsiteRelation;
    }

    /**
     * Generate document url rewrite
     *
     * @param Document $document
     *
     * @return UrlRewrite[]
     */
    public function generate(Document $document)
    {
        $websites = $this->storeManager->getWebsites();
        $stores = [];
        foreach ($websites as $website) {
            $stores = array_merge(
                $stores,
                $this->storeWebsiteRelation->getStoreByWebsiteId(
                    $website->getId()
                )
            );
        }

        return in_array(0, $stores) ? $this->generateForSpecificStores($stores, $document)
            : $this->generateForAllStores($document);
    }

    /**
     * Generate list of urls for default store
     *
     * @param Document $document
     *
     * @return UrlRewrite[]
     */
    protected function generateForAllStores(Document $document)
    {
        $urls = [];
        foreach ($this->storeManager->getStores() as $store) {
            $urls[] = $this->createUrlRewrite($store->getStoreId(), $document);
        }

        return $urls;
    }

    /**
     * Generate list of urls per store
     *
     * @param array $storeIds
     * @param Document $document
     *
     * @return UrlRewrite[]
     */
    protected function generateForSpecificStores(array $storeIds, Document $document)
    {
        $urls = [];
        $existingStores = $this->storeManager->getStores();
        foreach ($storeIds as $storeId) {
            if (!isset($existingStores[$storeId])) {
                continue;
            }
            $urls[] = $this->createUrlRewrite($storeId, $document);
        }

        return $urls;
    }

    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param int $redirectType
     *
     * @return UrlRewrite
     */
    protected function createUrlRewrite($storeId, $document, $redirectType = 301)
    {
        return $this->urlRewriteFactory->create()->setStoreId($storeId)
            ->setEntityType(self::ENTITY_TYPE)
            ->setEntityId($document->getEntityId())
            ->setRequestPath($document->getUrlRewrite())
            ->setTargetPath($this->documentUrlPathGenerator->getCanonicalUrlPath($document))
            ->setIsAutogenerated(1)
            ->setRedirectType($redirectType);
    }
}
