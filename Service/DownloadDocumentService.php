<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Service;

use Exception;
use Anhvdk\DocumentManagement\Model\Document\FileInfo;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document\Collection;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document\CollectionFactory as DocumentCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Magento\Framework\App\ResponseInterface;
use ZipArchive;

class DownloadDocumentService
{
    const CONTENT_TYPE = 'application/zip';

    /**
     * @var DocumentCollection
     */
    protected DocumentCollection $documentCollection;

    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    /**
     * @var FileInfo
     */
    protected FileInfo $fileInfo;

    /**
     * @param DocumentCollection $documentCollection
     * @param FileFactory $fileFactory
     * @param FileInfo $fileInfo
     */
    public function __construct(
        DocumentCollection $documentCollection,
        FileFactory $fileFactory,
        FileInfo $fileInfo
    ) {
        $this->documentCollection = $documentCollection;
        $this->fileFactory = $fileFactory;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Download file by document ids
     *
     * @param array $ids
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function downloadFileByDocumentIds(array $ids)
    {
        $zipArchive = new ZipArchive();
        $zipArchive->open($this->fileInfo->getZipFileFullPath(), ZipArchive::CREATE);
        $documentList = $this->getDocumentList($ids);
        foreach ($documentList as $document) {
            $fileName = $document->getFileName();
            if ($fileName && $this->fileInfo->documentIsExisted($fileName)) {
                $filepath = $this->fileInfo->getDocumentPath($fileName, true);
                $zipArchive->addFile($filepath, basename($filepath));
            }
        }
        $zipArchive->close();
        $zipFileName = $this->fileInfo->getZipFileName();
        return $this->fileFactory->create(
            $zipFileName,
            [
                'type' => 'filename',
                'value' => $this->fileInfo->getZipFileMiniPath(),
                'rm' => true
            ],
            DirectoryList::MEDIA,
            self::CONTENT_TYPE
        );
    }

    /**
     * Get collection of document by ids
     *
     * @param array $ids
     *
     * @return Collection
     */
    protected function getDocumentList(array $ids)
    {
        $collection = $this->documentCollection->create();
        $collection->addFieldToFilter(
            DocumentInterface::ENTITY_ID,
            [
                'in' => $ids
            ]
        );

        return $collection;
    }
}
