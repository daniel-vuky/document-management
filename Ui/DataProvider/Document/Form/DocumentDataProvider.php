<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Ui\DataProvider\Document\Form;

use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Anhvdk\DocumentManagement\Api\DocumentRepositoryInterface;
use Anhvdk\DocumentManagement\Model\Document\FileInfo;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document\CollectionFactory as DocumentCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DocumentDataProvider
 */
class DocumentDataProvider extends AbstractDataProvider
{
    /**
     * Request Interface
     *
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * Document Repository
     *
     * @var DocumentRepositoryInterface
     */
    private DocumentRepositoryInterface $documentRepository;

    /**
     * Serializer
     *
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * File Info
     *
     * @var FileInfo
     */
    protected FileInfo $fileInfo;

    /**
     * Store Manager Interface
     *
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param DocumentCollectionFactory $documentCollection
     * @param RequestInterface $request
     * @param DocumentRepositoryInterface $documentRepository
     * @param StoreManagerInterface $storeManager
     * @param FileInfo $fileInfo
     * @param Serializer $serializer
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DocumentCollectionFactory $documentCollection,
        RequestInterface $request,
        DocumentRepositoryInterface $documentRepository,
        StoreManagerInterface $storeManager,
        FileInfo $fileInfo,
        Serializer $serializer,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $documentCollection->create();
        $this->request = $request;
        $this->documentRepository = $documentRepository;
        $this->serializer = $serializer;
        $this->fileInfo = $fileInfo;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $documentId = $this->request->getParam('id', false);
        if ($documentId) {
            $documentModel = $this->documentRepository->get((int) $documentId);
            $documentData = $documentModel->getData();
            if ($documentModel->getPreviewImage()) {
                $documentData[DocumentInterface::PREVIEW_IMAGE]
                    = $this->preparePreviewImage($documentModel->getPreviewImage());
            }
            if ($documentModel->getFileName()) {
                $documentData[DocumentInterface::FILE_NAME]
                    = $this->prepareFile($documentModel->getFileName());
            }
            $documentData[DocumentInterface::WEBSITE_IDS]
                = trim($documentModel->getWebsiteIds(), ',');
            $this->data[$documentId] = $documentData;
        }

        return $this->data;
    }

    /**
     * Prepare preview image
     *
     * @param string $previewImageName
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws FileSystemException
     */
    protected function preparePreviewImage(string $previewImageName)
    {
        if ($this->fileInfo->isExist($previewImageName, 'image')) {
            $stat = $this->fileInfo->getStat($previewImageName);
            $mime = $this->fileInfo->getMimeType($previewImageName);
            $imageData = [];
            $imageData[0]['old_file'] = basename($previewImageName);
            $imageData[0]['file'] = basename($previewImageName);
            $imageData[0]['name'] = basename($previewImageName);
            $imageData[0]['url'] = $this->getImageUrl($previewImageName);
            $imageData[0]['size'] = isset($stat) ? $stat['size'] : 0;
            $imageData[0]['type'] = $mime;
            return $imageData;
        }

        return [];
    }

    /**
     * Get image url
     *
     * @param string $imageFileName
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getImageUrl(string $imageFileName)
    {
        $store = $this->storeManager->getStore();
        $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaBaseUrl . trim(FileInfo::ENTITY_MEDIA_PATH, '/') . '/' . $imageFileName;
    }

    /**
     * Prepare file array
     *
     * @param string $fileName
     *
     * @return array
     * @throws FileSystemException
     */
    protected function prepareFile(string $fileName)
    {
        if ($this->fileInfo->isExist($fileName, 'file')) {
            $stat = $this->fileInfo->getStat($fileName, 'file');
            $mime = $this->fileInfo->getMimeType($fileName, 'file');
            $fileData = [];
            $fileData[0]['old_file'] = basename($fileName);
            $fileData[0]['file'] = basename($fileName);
            $fileData[0]['name'] = basename($fileName);
            $fileData[0]['url'] = $this->getFilePath($fileName);
            $fileData[0]['size'] = isset($stat) ? $stat['size'] : 0;
            $fileData[0]['type'] = $mime;
            return $fileData;
        }

        return [];
    }

    /**
     * Get file path
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function getFilePath(string $fileName)
    {
        return $this->fileInfo
            ->getMediaDirectory()
            ->getAbsolutePath(
                $this->fileInfo->getFilePath($fileName, 'file')
            );
    }
}
