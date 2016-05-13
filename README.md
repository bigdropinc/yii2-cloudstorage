# Yii2 Cloud Storage
Common interface for amazon s3 and rackspace cloud files.
## Configuration
### Amazon S3
Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    //...
    'components' => [
        //...
        'cloudStorage' => [
            'class' => 'bigdropinc\cloudStorage\AmazonS3',
            'key' => '***',
            'secret' => '***',
            'bucket' => 'bucket name',
            //fill this attribute, if you know the base url of your bucket
            'cloudStorageBaseUrl' => 'http://site.s3.amazon.com/'
        ],
    ],
];
```
### Rackspace Cloud Files
Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    //...
    'components' => [
        //...
        'cloudStorage' => [
            'class' => 'bigdropinc\cloudStorage\RackspaceCloudFiles',
            'username' => '***',
            'apiKey' => '***',
            'region' => 'LON',
            'containerName' => 'container name',
            //fill this attribute, if you know the base url of your container
            'cloudStorageBaseUrl' => 'http://site.rackspace.com/'
        ],
    ],
];
```
## Usage
### Uploading Files
```php
$file = '@frontend/web/media/image.png';
Yii::$app->cloudStorage->upload($file);
```

### Downloading Files
```php
$name = 'media/image.png';
$dir = '@frontend/web/tmp';
Yii::$app->cloudStorage->download($name, $dir);
```
### Deleting Files
```php
$name = 'media/image.png';
Yii::$app->cloudStorage->delete($name);
```
### Getting Public Url
```php
$name = 'media/image.png';
Yii::$app->cloudStorage->getPublicUrl($name);
```