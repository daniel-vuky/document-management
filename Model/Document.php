<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model;

use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Document Model
 */
class Document extends AbstractModel implements DocumentInterface, IdentityInterface
{
    const CACHE_TAG = 'Anhvdk_document';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Anhvdk_document';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Document::class);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return (int) $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getTagging()
    {
        return $this->getData(self::TAGGING);
    }

    /**
     * @inheritDoc
     */
    public function setTagging(string $tagging)
    {
        return $this->setData(self::TAGGING, $tagging);
    }

    /**
     * @inheritDoc
     */
    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setShortDescription(string $shortDescription)
    {
        return $this->setData(self::SHORT_DESCRIPTION, $shortDescription);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return (int) $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteIds()
    {
        return $this->getData(self::WEBSITE_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteIds(string $websiteIds)
    {
        return $this->setData(self::WEBSITE_IDS, $websiteIds);
    }

    /**
     * @inheritDoc
     */
    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setFileName(string $fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }

    /**
     * @inheritDoc
     */
    public function getPreviewImage()
    {
        return $this->getData(self::PREVIEW_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setPreviewImage(string $previewImage)
    {
        return $this->setData(self::PREVIEW_IMAGE, $previewImage);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return (int) $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder(int $sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function getUrlRewrite()
    {
        return $this->getData(self::URL_REWRITE);
    }

    /**
     * @inheritDoc
     */
    public function setUrlRewrite(string $urlRewrite)
    {
        return $this->setData(self::URL_REWRITE, $urlRewrite);
    }

    /**
     * @inheritDoc
     */
    public function getPublishedDate()
    {
        return $this->getData(self::PUBLISHED_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setPublishedDate(string $publishedDate)
    {
        return $this->setData(self::PUBLISHED_DATE, $publishedDate);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        // Convert websites
        if ($this->hasWebsiteIds()) {
            $websiteIds = $this->getWebsiteIds();
            $websiteIds = is_array($websiteIds) ? implode(',', $websiteIds) : $websiteIds;
            $this->setWebsiteIds($websiteIds);
        }

        $previewImageConfig = $this->getPreviewImage();
        if ($previewImageConfig && isset($previewImageConfig[0]['file'])) {
            $this->setPreviewImage($previewImageConfig[0]['file']);
        }
        $fileConfig = $this->getFileName();
        if ($fileConfig && isset($fileConfig[0]['file'])) {
            $this->setFileName($fileConfig[0]['file']);
        }

        // Set url rewrite
        if (!$this->getUrlRewrite()) {
            $name = $this->getName() ?: '';
            $urlRewrite = str_replace(' ', '-', strtolower($name));
            $this->setUrlRewrite($urlRewrite);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }
}
