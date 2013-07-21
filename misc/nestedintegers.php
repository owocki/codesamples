<?

/**
* This class performs operations on NestedInteger arrays.
* @see http://url.to/requirements
* @author Kevin Owocki <ksowocki@gmail.com>
*/
class NestedIntegerOperations {

    /** 
     * Given a nested list of integers, returns the sum of all integers in the list weighted by their depth
     * For example, given the list {{1,1},2,{1,1}} the function should return 10 (four 1's at depth 2, one 2 at depth 1)
     * Given the list {1,{4,{6}}} the function should return 27 (one 1 at depth 1, one 4 at depth 2, and one 6 at depth 3)
     */
    public static depthSum ($input,$depth=1) {
        
        //if(!is_a($input,'NestedInteger')
        if(!is_array($input)){
            return 0;
        }
        
        //define holder variable upfront
        $currentSum = 0;
        //iterate through NestedInteger
        foreach($input as $i => $element){
            if(is_array($element){
                $currentSum += NestedIntegerOperations::depthSum($element, $depth+1 );
            } elseif(is_int($element)){
                $currentSum += ($depth * $element);
            } else { 
                /* something went wrong, we have neither an array nor a integer in this element! */ 
               throw new NestedIntegerException("at depth $depth, there is an element at key $i that is not an integer or an array!");
             }
            
        }
        
        /* NOTE: if this was a java implementation, we would do some sort of garbage collection here. PHP is a higher level language, so it is not needed. */
        return $currentSum;
    
    }

}

class NestedIntegerException extends Exception;
 

/**
* Testing class for depthSum function
* @see NestedIntegerOperations
*/
class NestedIntegerDepthSumTest extends PHPUnit_Framework_testCase{


    /**
     * Test that an empty list returns 0
     */
    public function testEmptyList(){
        $expectedResult = 0;
        $arr = array( ); 
        $sum = depthSum($arr);
        $this->assertEquals($expectedResult,$sum);
    }    
    /**
     * Given the list {{1,1},2,{1,1}} the function should return 10 (four 1's at depth 2, one 2 at depth 1)
     */
    public function testExample1(){
        $expectedResult = 10;
        $arr = array( array(1,1), 2, array(1,1) ); 
        $sum = depthSum($arr);
        $this->assertEquals($expectedResult,$sum);
    }
    /**
     * Test that a NestedIntegerException is thrown when bad input is given
     */
    public function testBadInput_nonIntElement(){
        try{
            $arr = array( 1.11 ); 
            $sum = depthSum($arr);
        } catch ($NestedIntegerException){
            $this->pass();
        }
        $this->fail('NestedIntegerException not thrown');
    }

    /**
     * Test that a NestedIntegerException is thrown when bad input is given
     */
    public function testBadInput_nonNestedInteger(){
        $expectedResult = 0;
        $arr = "hire kevin!"; 
        $sum = depthSum($arr);
        $this->assertEquals($expectedResult,$sum);
    }

    /**
     * Given the list {1,{4,{6}}} the function should return 27 (one 1 at depth 1, one 4 at depth 2, and one 6 at depth 3)
     */
    public function testExample2(){
        $expectedResult = 27;
        $arr = array( 1, array(4, array( 6) ) ); 
        $sum = depthSum($arr);
        $this->assertEquals($expectedResult,$sum);
    }


}




?>
