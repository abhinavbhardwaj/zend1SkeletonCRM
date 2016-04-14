<?php
/**
 *Common function file for the application
 */

/**
 *function to print and die in a mannarful way
 *@param:String $str to be print Bool $notDie Desice need to die or not
 *@author: Abhinav
 */
function prd($str, $notDie=FALSE){
    echo "<pre>";
    print_r($str);
    echo "</pre>";
    if($notDie===FALSE) die;
}

/*
 *Provide a bootstrap button as per the given status
 *@param: str button
 *@return: bootstrap button
 *@author: Abhinav
 */
function getStatusButton($status){
    $button = "";
        switch($status):
            case("open"):
                $button = '<button type="button" class="btn btn-sm btn-default">Open</button>';
            break;
            case("in-progress"):
                $button = '<button type="button" class="btn btn-sm btn-info">In-progress</button>';
            break;
            case("complete"):
                $button = '<button type="button" class="btn btn-sm btn-danger" title="Payment Due">Completed</button>';
            break;

        endswitch;
        return $button;
}

/**
 *Function to convert amoiunt into words
 *@param: float Amount
 *@return Str amount in words
 *@author: Abhinav
 */
function amountInWords($amount){
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
	$inWords =  $f->format($amount);
    return  ucfirst($inWords);
}