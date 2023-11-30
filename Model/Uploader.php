<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\File\Name;

/**
 * Class Uploader
 */
class Uploader
{
    const ALLOW_TYPES = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
        'ppt',
        'pptx',
        'zip',
        'mp3',
        'mp4',
        'mov',
        'mpg',
        'mpeg',
        'jpg',
        'jpeg',
        'gif',
        'png',
        'bmp',
        'tif',
        'tiff',
        'psd',
        'ai',
        'eps'
    ];
    const BASE_FOLDER = 'Anhvdk/document-file';
    const BASE_TMP_FOLDER = 'tmp/document-file';

    /**
     * Filesystem
     *
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * UploaderFactory
     *
     * @var UploaderFactory
     */
    private UploaderFactory $fileUploaderFactory;

    /**
     * ManagerInterface
     *
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * RequestInterface
     *
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var Name
     */
    private Name $fileNameLookup;

    /**
     * @var Database
     */
    private Database $coreFileStorageDatabase;

    /**
     * Uploader constructor.
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        ManagerInterface $messageManager,
        RequestInterface $request,
        Name $name,
        Database $coreFileStorageDatabase
    ) {
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->fileNameLookup = $name;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
    }

    /**
     * Upload file to media
     *
     * @return array
     * @throws FileSystemException
     */
    public function upload(): array
    {
        $result = [];
        $directoryWrite = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $directory = $directoryWrite->getAbsolutePath(self::BASE_TMP_FOLDER);
        $file = $this->request->getFiles('file_name');
        $uploader = $this->fileUploaderFactory->create(['fileId' => $file]);
        $uploader->setAllowedExtensions(self::ALLOW_TYPES);
        $uploader->setAllowRenameFiles(true);

        try {
            $result = $uploader->save($directory);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $result;
    }

    /**
     * Move file from tmp
     *
     * @param string $fileName
     *
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function moveFileFromTmp(string $fileName)
    {
        $baseTmpPath = self::BASE_TMP_FOLDER;
        $basePath = self::BASE_FOLDER;
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $baseImagePath = $this->getFilePath(
            $basePath,
            $this->fileNameLookup->getNewFileName(
                $mediaDirectory->getAbsolutePath(
                    $this->getFilePath($basePath, $fileName)
                )
            )
        );
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $fileName);

        try {
            $this->coreFileStorageDatabase->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new LocalizedException(__('Something went wrong while saving the file(s).'), $e);
        }

        return $fileName;
    }

    /**
     * Get file path
     *
     * @param $path
     * @param $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }
}
