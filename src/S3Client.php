<?php

namespace grptx;

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

    public $use_path_style_endpoint = true;

    /**
     * @return \Aws\S3\S3Client
     */
    public function getS3Client(): \Aws\S3\S3Client
    {
        if(!$this->S3Client) {
            $this->S3Client = new \Aws\S3\S3Client([
                'credentials' => [
                    'key'    => $this->key ?? '',
                    'secret' => $this->secret ?? '',
                ],
                'region' => $this->region ?? '',
                'version' => $this->version ?? 'latest',
                'endpoint' => $this->endpoint ?? '',
                'use_path_style_endpoint' => true,
            ]);
        }

        return $this->S3Client;
    }

    /**
     * @param string $content
     * @param string $storageSavePath
     * @param string $bucket
     * @return Result|bool
     */
    public function putObjectContent(string $content, string $storageSavePath, string $bucket)
    {
        try {
            $storageSavePath = $this->formatStorageSavePath($storageSavePath);

            $result = $this->S3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $storageSavePath,
                'Body'   => $content
            ]);

            return $result;
        } catch (AwsException $awsException) {
            return false;
        }
    }

    /**
     * @param string $storageSavePath
     * @author klinson <klinson@163.com>
     * @return string
     */
    private function formatStorageSavePath(string $storageSavePath)
    {
        return trim($storageSavePath, '/');
    }
}