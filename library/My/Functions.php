<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class My_Functions
{

    public static function generateImages($ImageNameArray, $dispalyTextWithTag)
    {
        $s3 = new My_Service_Amazon_S3();
        $arrImages = explode(',', $ImageNameArray);
        $arrStrChunks = explode('equation_tags', $dispalyTextWithTag);
        $strDesc = $arrStrChunks[0];
        for ($m = 1; $m < count($arrStrChunks); $m++) {
            $strDesc .= "<img src='data:image/gif;base64," . base64_encode($s3->getFileContent('equation/' . $arrImages[$m - 1])) . "' /> ";
            $strDesc .= $arrStrChunks[$m];
        }
        return $strDesc;
    }

    public static function generateAndroidView($ImageNameArray, $dispalyTextWithTag)
    {
        $s3 = new My_Service_Amazon_S3();
        $arrImages = explode(',', $ImageNameArray);
        $arrStrChunks = explode('equation_tags', $dispalyTextWithTag);
        $strDesc = '<html><body><p style="font-style:Latin Modern 10 Regular; font-weight:normal; font-size:20px;">' . $arrStrChunks[0];
        for ($m = 1; $m < count($arrStrChunks); $m++) {
            $strDesc .= "<img style ='vertical-align:middle' src='data:image/gif;base64," . base64_encode($s3->getFileContent('equation/' . $arrImages[$m - 1])) . "' />";
            $strDesc .= $arrStrChunks[$m];
        }

        $strDesc .= '</p></body></html>';
        return $strDesc;
    }

    /**
     * Converts seconds to HH:MM:SS
     * @param Int $seconds
     * @return String
     */
    public static function secondsToHHMMSS($seconds, $showSeconds = TRUE)
    {
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        $s = $seconds % 60;

        if ($showSeconds) {
            return sprintf("%02d:%02d:%02d", $H, $i, $s);
        }

        return sprintf("%02d:%02d", $H, $i);
    }

    /**
     * Converts seconds to HH:MM:SS
     * @param Int $seconds
     * @return String
     */
    public static function secondsToMMSS($seconds, $showSeconds = TRUE)
    {
        $i = ($seconds / 60);
        $s = $seconds % 60;

        if ($showSeconds) {
            return sprintf("%02d:%02d", $i, $s);
        }

        return sprintf("%02d", $i);
    }

    public static function generateRandomString($length = NULL)
    {
        if (empty($length)) {
            $length = rand(8, 50);
        }
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
