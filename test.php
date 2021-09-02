<?php 

function checkPalindrome($angka){
    $temp = $angka; 
    $new = 0; 
    while (floor($temp)) {
		
        $d = $temp % 10; 
        $new = $new * 10 + $d; 
        $temp = $temp/10; 
    } 
	
	echo $new;
    if ($new == $angka){ 
        return 1; 
    }
    else{
        return 0;
    }
}

	$aryPalindrome = array();
for ($i=1;$i<=1000;$i++){	
	$palindrome = checkPalindrome($i); 
	if($palindrome == 1){ 
		$aryPalindrome[$i] = $i;
	}
}


$a = 8;
$b = 10; 

$c = 8*$a+3*$b;


?>