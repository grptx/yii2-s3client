<?php

namespace grptx;

use Aws\Acm\Exception\AcmException;
use Aws\Exception\AwsException;
use Aws\Result;
use yii\base\Component;

class S3Client extends Component
{
    /** @var \Aws\S3\S3Client */
    private $S3Client;

    public $key;

    public $secret;

    public $region;

    public $version = 'latest';

    public $endpoint;

    public $profile = 'default';

    public $defaultBucket;

    public $debug = false;

    public $http = [
        'verify' => true
    ];

    public function init()
    {
        parent::init();
        if (!$this->S3Client) {
            $this->getS3Client();
        }
    }


    /**
     * @return \Aws\S3\S3Client
     */
    public function getS3Client(): \Aws\S3\S3Client
    {
        if (!$this->S3Client) {
            $this->S3Client = new \Aws\S3\S3Client([
                'credentials' => [
                    'key' => $this->key ?? '',
                    'secret' => $this->secret ?? '',
                ],
                'region' => $this->region ?? '',
                'version' => $this->version ?? 'latest',
                'endpoint' => $this->endpoint ?? '',
                'use_path_style_endpoint' => true,
                'debug' => $this->debug,
                'http' => $this->http
            ]);
        }

        return $this->S3Client;
    }

    public function createBucket(String $bucket = null)
    {
        if (is_null($bucket)) {
            return false;
        }
        try {
            $result = $this->S3Client->createBucket([
                'Bucket' => $bucket,
            ]);
            return $bucket;
        } catch (AwsException $awsException) {
            return false;
        }
    }

    /**
     * put file to minio/s3 server
     *
     * @param string $localObjectPath full path to file to put
     * @param string|null $storageSavePath full path to file in bucket (optional)
     * @param string|null $bucket the bucket name (optional)
     * @param array $meta
     * @param array $tags
     * @return Result|bool
     * @throws AcmException
     */
    public function putObjectByPath(string $localObjectPath, string $storageSavePath = null, string $bucket = null, array $meta = [], array $tags = [])
    {
        if (is_null($bucket)) {
            $bucket = $this->defaultBucket;
        }

        if (empty($bucket)) {
            return false;
        }

        $this->createBucket($bucket);

        if ($storageSavePath === null) {
            $storageSavePath = $localObjectPath;
        }

        $meta = $this->cleanMeta($meta);
        $tags = $this->normalizeTags($tags);


        $storageSavePath = $this->formatStorageSavePath($storageSavePath);

        $result = $this->S3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $storageSavePath,
            'SourceFile' => $localObjectPath,
            'Metadata' => $meta,
            'Tagging' => $tags,
        ]);

        return $result;

    }

    /**
     * create and put a file into minio/s3 server with the specified content
     *
     * @param string $content
     * @param string $storageSavePath
     * @param string $bucket
     * @param array $meta
     * @param array $tags
     * @return Result|bool
     * @throws AcmException
     */
    public function putObjectByContent(string $content, string $storageSavePath, string $bucket = null, array $meta = [], array $tags = [])
    {
        if (is_null($bucket)) {
            $bucket = $this->defaultBucket;
        }

        if (empty($bucket)) {
            return false;
        }

        $this->createBucket($bucket);

        $meta = $this->cleanMeta($meta);
        $tags = $this->normalizeTags($tags);


        $storageSavePath = $this->formatStorageSavePath($storageSavePath);

        $result = $this->S3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $storageSavePath,
            'Body' => $content,
            'Metadata' => $meta,
            'Tagging' => $tags,
        ]);

        return $result;

    }

    /**
     * get file object from minio/s3 server
     *
     * @param string $storageSavePath
     * @param string|null $localSaveAsPath
     * @param string|null $bucket
     * @return bool|mixed
     */
    public function getObject(string $storageSavePath, string $localSaveAsPath = null, string $bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->defaultBucket;
        }

        if (empty($bucket)) {
            return false;
        }

        try {
            $param = [
                'Bucket' => $bucket,
                'Key' => $storageSavePath,
            ];
            if (!is_null($localSaveAsPath)) {
                $param[] = [
                    'SaveAs' => $localSaveAsPath
                ];
            }

            $result = $this->S3Client->getObject($param);
            return $result['Body'];
        } catch (AwsException $awsException) {
            return false;
        }

    }

    /**
     * @param string $storageSavePath
     * @return string
     * @author klinson <klinson@163.com>
     */
    private function formatStorageSavePath(string $storageSavePath)
    {
        return trim($storageSavePath, '/');
    }

    /**
     * @param array $meta
     * @return array
     */
    private function cleanMeta(array $meta): array
    {
        if (!empty($meta)) {
            foreach ($meta as $k => $v) {
                unset($meta[$k]);
                if (is_null($v)) continue;
                $v = (string)$v;
                $meta[$k] = $v;
            }
        }
        return $meta;
    }

    private function normalizeTags(array $tags): string
    {
        if (empty($tags)) return null;
        return http_build_query($tags);
    }
}