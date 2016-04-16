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
function amountInWordss($amount){
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
	$inWords =  $f->format($amount);
    return  ucfirst($inWords);
}


/**
 *Function to convert amoiunt into words
 *@param: float Amount
 *@return Str amount in words
 *@author: Abhinav
 */
$ones =array('',' One',' Two',' Three',' Four',' Five',' Six',' Seven',' Eight',' Nine',' Ten',' Eleven',' Twelve',' Thirteen',' Fourteen',' Fifteen',' Sixteen',' Seventeen',' Eighteen',' Nineteen');
$tens = array('','',' Twenty',' Thirty',' Fourty',' Fifty',' Sixty',' Seventy',' Eighty',' Ninety',);
$triplets = array('',' Thousand',' Lac',' Crore',' Arab',' Kharab');


function amountInWords( $num = '' ){
 global $ones, $tens, $triplets;
$str ="";


//$num =(int)$num;
$th= (int)($num/1000);
$x = (int)($num/100) %10;
$fo= explode('.',$num);

if($fo[0] !=null){
$y=(int) substr($fo[0],-2);

}else{
    $y=0;
}

if($x > 0){
    $str =$ones[$x].' Hundred';

}
if($y>0){
if($y<20)
{
 $str .=$ones[$y];

}
else {
    $str .=$tens[($y/10)].$ones[($y%10)];
   }
}
$tri=1;
while($th!=0){

    $lk = $th%100;
    $th = (int)($th/100);
    $count =$tri;

    if($lk<20){
        if($lk == 0){
        $tri =0;}
        $str = $ones[$lk].$triplets[$tri].$str;
        $tri=$count;
        $tri++;
    }else{
        $str = $tens[$lk/10].$ones[$lk%10].$triplets[$tri].$str;
        $tri++;
    }
}
$num =(float)$num;
if(is_float($num)){
     $fo= (String) $num;
      $fo= explode('.',$fo);
       $fo1= @$fo[1];

}else{
    $fo1 =0;
}
$check = (int) $num;
 if($check !=0){
          return $str.' Rupees'.forDecimal($fo1);
    }else{
       return forDecimal($fo1);
    }
}

//function for decimal parts
 function forDecimal($num){
    global $ones,$tens;
    $str="";
    $len = strlen($num);
    if($len==1){
        $num=$num*10;
    }
    $x= $num%100;
    if($x>0){
    if($x<20){
        $str = $ones[$x].' Paise';
    }else{
        $str = $ones[$x/10].$ones[$x%10].' Paise';
    }
    }
     return $str;
 }