<?php
$Array = array(56,57,58,59,60,61,62,63,64,65);
	foreach($Array as $arr){
    	echo "$arr ";
	}	 	
    echo "<br>";
    echo "</n><br>".'Printing Even numbers Only';
		foreach($Array as $numbers){
    		if(($numbers % 2) == 0 ){
        		echo "<li>".$numbers."</li>";
        	}
    	}
?>     
