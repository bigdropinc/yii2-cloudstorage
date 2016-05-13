<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bigdropinc\cloudstorage;

/**
 * Interface CloudStorageInterface
 * @package bigdropinc\cloudstorage
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
interface CloudStorageInterface
{
    /**
     * Saves a file to cloud storage
     * @param string $file the file uploaded.
     * The [[UploadedFile::$tempName]] will be used as the source file.
     */
    public function upload($file);

    /**
     * Downloads file(s) to the local filesystem
     *
     * @param string $prefix Only download objects that use this key prefix
     * @param string $dir Directory to download to
     */
    public function download($prefix = '', $dir = null);
    
    /**
     * Removes a file
     * @param string|array $file the path of the file(s) to remove
     * @return int number of deleted files
     */
    public function delete($file);

    /**
     * Returns the public url of the file or empty string if the file does not exists.
     * @param string $file the path of the file to access
     * @return string
     */
    public function getPublicUrl($file);

    /**
     * @return mixed
     */
    public function getClient();
}
