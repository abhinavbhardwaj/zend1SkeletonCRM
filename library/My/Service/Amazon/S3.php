<?php

/**
 * Amazon S3 API Wrapper
 * 
 * @author Ashwini Agarwal
 */
class My_Service_Amazon_S3 extends Zend_Service_Amazon_S3
{

    public function __construct()
    {
        parent::__construct(AWS_S3_ACCESS_KEY, AWS_S3_SECRET_KEY);

        if (!$this->isBucketAvailable(AWS_S3_BUCKET)) {
            throw new Zend_Exception('AWS S3 bucket not found');
        }

        static $isStreamWrapperRegistered = FALSE;
        if (!$isStreamWrapperRegistered) {
            $this->registerStreamWrapper();
            $isStreamWrapperRegistered = TRUE;
        }
    }

    public function save($content, $destination)
    {
        $s3Destination = AWS_S3_BUCKET . '/' . ltrim($destination, '/');
        $response = $this->putObject($s3Destination, $content, array(
            Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ
        ));

        return $response;
    }

    public function saveFile($file, $destination)
    {
        $s3Destination = AWS_S3_BUCKET . '/' . ltrim($destination, '/');
        $response = $this->putFile($file, $s3Destination, array(
            Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ
        ));
        return $response;
    }

    public function getFileContent($object)
    {
        return file_get_contents("s3://" . AWS_S3_BUCKET . '/' . ltrim($object, '/'));
    }

    public function delete($destination)
    {
        $s3Destination = AWS_S3_BUCKET . '/' . ltrim($destination, '/');
        if ($this->isObjectAvailable($s3Destination)) {
            $this->removeObject($s3Destination);
            return TRUE;
        }

        return FALSE;
    }

}
