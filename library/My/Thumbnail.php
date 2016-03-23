<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
require_once APPLICATION_PATH . '/../library/wideimage-11.02.19-lib/WideImage.php';

class My_Thumbnail
{

    /**
     * 
     * @param type $image
     * @param type $thumb
     * @param type $thumbWidth
     * @param type $thumbHeight
     */
    public static function createThumb($image, $thumb, $thumbWidth = 100, $thumbHeight = NULL)
    {
        // replace image
        $wide = WideImage::load($image);
        $resized = $wide->resize($thumbWidth, $thumbHeight, 'fill');
        $resized->saveToFile($thumb);
    }

    public static function getThumbnail($image, $format, $thumbWidth = 100, $thumbHeight = NULL)
    {
        $wide = WideImage::load($image);
        $resized = $wide->resize($thumbWidth, $thumbHeight, 'fill');
        return $resized->asString($format);
    }

    /**
     * 
     * @param type $imageData
     * @param type $thumb
     * @param type $thumbWidth
     * @param type $thumbHeight
     * @return boolean
     */
    public static function createThumbFromDataStream($imageData, $format, $thumbWidth = NULL, $thumbHeight = NULL)
    {
        if (strpos($imageData, 'data:image') === FALSE) {
            return FALSE;
        }

        $image = substr($imageData, strpos($imageData, ",") + 1);
        return self::getThumbnail(base64_decode($image), $format, $thumbWidth, $thumbHeight);
    }

}
