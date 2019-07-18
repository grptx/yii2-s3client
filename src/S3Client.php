<?php

namespace grptx;

use yii\base\Component;

class S3Client extends Component
{
    /** @var \Aws\S3\S3Client */
    private $S3Client;

    public $key;

    public $secret;

    public $region;

    public $version;

    public $endpoint;

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
                'version' => $this->version ?? '',
                'endpoint' => $this->endpoint ?? '',
                'use_path_style_endpoint' => true,
            ]);
        }

        return $this->S3Client;
    }


}