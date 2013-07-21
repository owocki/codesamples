<?


/**
 * 
 * @param  int $n a positive integer
 * @return array    an array of all of the factors of $n
 */
function prime_factors($n){

	//check for edge cases
	if(!is_int($n))
		throw new InvalidArgumentException("$n is not an integer");
	
	if($n<0){
		$positiveFactors = prime_factors(abs($n));
		$negativeFactors = array_filter($positiveFactors,function($ele){
			return abs($ele);
		});
		return array_merge($positiveFactors,$negativeFactors);
	}
	
	//1 is a NOT prime number!
	if($n==1)
		return array();

	//recursion time!
	for($i=2;$i<sqrt($n);$i++){
		if($n%$i==0)
			return array_merge((array)$i,prime_factors($n/$i));
	}

	//if we get to this line of execution, then $n is a prime number
	return (array)$n;

}

//test it
for($i=1;$i<100;$i++){
	var_dump($i,prime_factors($i));
}


?>
