# yii2-S3Client

[![Latest Stable Version](https://poser.pugx.org/grptx/yii2-s3client/v/stable)](https://packagist.org/packages/grptx/yii2-s3client)
[![Total Downloads](https://poser.pugx.org/grptx/yii2-s3client/downloads)](https://packagist.org/packages/grptx/yii2-s3client)
[![Latest Unstable Version](https://poser.pugx.org/grptx/yii2-s3client/v/unstable)](https://packagist.org/packages/grptx/yii2-s3client)
[![License](https://poser.pugx.org/grptx/yii2-s3client/license)](https://packagist.org/packages/grptx/yii2-s3client)
[![composer.lock available](https://poser.pugx.org/grptx/yii2-s3client/composerlock)](https://packagist.org/packages/phpunit/phpunit)

Yii2 S3Client based on [klinson/aws-s3-minio](https://github.com/klinson/aws-s3-minio)

## Configuration

in ``web.php``

```php
'components'=> [
    's3client' => [
        'class'=> 'grptx\S3Client',
        'key' => '<your key>',
        'secret' => '<yout secret>',
        'endpoint'=> '<your endpoint>',
        'defaultBucket' => '<bucket>', //optional default bucket
    ],
],
```

## Usage

```
/** @var S3Client $s3client */
$s3client = Yii::$app->s3client;

/**
 * put file to minio/s3 server
 * 
 * @param string $localObjectPath full path to file to put
 * @param string|null $storageSavePath full path to file in bucket (optional)
 * @param string|null $bucket the bucket name (optional)
 * @return Result|bool
 */
$s3client->putObjectByPath(string $localObjectPath, string $storageSavePath = null, string $bucket = null);

/**
 * create and put a file into minio/s3 server with the specified content
 * 
 * @param string $content
 * @param string $storageSavePath
 * @param string $bucket
 * @return Result|bool
 */
$s3client->putObjectByContent(string $content, string $storageSavePath, string $bucket = null)

/**
 * get file object from minio/s3 server 
 * 
 * @param string $storageSavePath
 * @param string|null $localSaveAsPath
 * @param string|null $bucket
 * @return bool|mixed
 */
$s3client->getObject(string $storageSavePath, string $localSaveAsPath = null, string $bucket = null)
```