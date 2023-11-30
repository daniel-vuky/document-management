<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Api\Data;

/**
 * Document entity interface
 */
interface DocumentInterface
{
    const ENTITY_ID = 'entity_id';
    const NAME = 'name';
    const TAGGING = 'tagging';
    const SHORT_DESCRIPTION = 'short_description';
    const STATUS = 'status';
    const WEBSITE_IDS = 'website_ids';
    const FILE_NAME = 'file_name';
    const PREVIEW_IMAGE = 'preview_image';
    const SORT_ORDER = 'sort_order';
    const URL_REWRITE = 'url_rewrite';
    const PUBLISHED_DATE = 'published_date';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get Entity ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set Entity ID
     *
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId(int $entityId);

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name);

    /**
     * Get Tagging
     *
     * @return string|null
     */
    public function getTagging();

    /**
     * Set Tagging
     *
     * @param string $tagging
     *
     * @return $this
     */
    public function setTagging(string $tagging);

    /**
     * Get Short Description
     *
     * @return string|null
     */
    public function getShortDescription();

    /**
     * Set Short Description
     *
     * @param string $shortDescription
     *
     * @return $this
     */
    public function setShortDescription(string $shortDescription);

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status);

    /**
     * Get Website Ids
     *
     * @return string|null
     */
    public function getWebsiteIds();

    /**
     * Set Website Ids
     *
     * @param string $websiteIds
     *
     * @return $this
     */
    public function setWebsiteIds(string $websiteIds);

    /**
     * Get File Name
     *
     * @return string|null
     */
    public function getFileName();

    /**
     * Set File Name
     *
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName(string $fileName);

    /**
     * Get Preview Image
     *
     * @return string|null
     */
    public function getPreviewImage();

    /**
     * Set Preview Image
     *
     * @param string $previewImage
     *
     * @return $this
     */
    public function setPreviewImage(string $previewImage);

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder(int $sortOrder);

    /**
     * Get Url Rewrite
     *
     * @return string|null
     */
    public function getUrlRewrite();

    /**
     * Set Url Rewrite
     *
     * @param string $urlRewrite
     *
     * @return $this
     */
    public function setUrlRewrite(string $urlRewrite);

    /**
     * Set Published Date
     *
     * @param string $publishedDate
     *
     * @return $this
     */
    public function setPublishedDate(string $publishedDate);

    /**
     * Get Published Date
     *
     * @return string|null
     */
    public function getPublishedDate();

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created At
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(string $createdAt);

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt);
}
