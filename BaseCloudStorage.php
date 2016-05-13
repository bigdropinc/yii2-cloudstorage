<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bigdropinc\cloudstorage;

use yii\base\Component;

/**
 * Class BaseCloudStorage
 * @package bigdropinc\cloudstorage
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
abstract class BaseCloudStorage extends Component implements CloudStorageInterface
{
    public $localeStorageBasePath = '@frontend/web/';
    public $cloudStorageBaseUrl;
    
    protected $client;
    
    public function getName($file)
    {
        return str_replace(
            DIRECTORY_SEPARATOR,
            '/',
            str_replace(\Yii::getAlias($this->localeStorageBasePath), '', \Yii::getAlias($file))
        );
    }
}
