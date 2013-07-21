<?
/** 
* this class stores items in the current execution of PHP, rather than a remote service
* @package Library/Cache
* @author Kevin <kevin@stepout.com>
*/
class PHPCache extends Cache {
	
	private static $data = array();
	private static $expires = array();
	
	/**
	 * @param  $key - cache key
	 * Postconditions: expires $key
	 * 
	 * 
	 */
	public static function expire($key){
		unset(self::$data[$key]);
		unset(self::$expires[$key]);
	}
	
	/**
	 * @param  $key - cache key
	 * @return the value of $key in the cache
	 * 
	 * 
	 */
	public static function get($key){
		$start = microtime(true);

		if(isset(self::$expires[$key]) && self::$expires[$key] < time()){
			self::expire($key);

			self::log($key, false, microtime(true) - $start);

			return false;
		}

		self::log($key, isset(self::$data[$key]), microtime(true) - $start);

		return self::$data[$key];
	}
	
	/**
	 * @param  $key - cache key
	 * 			 $value - value to be cached
	 * 			 $expireTime (optonal) - how many seconds in the future should this key expire?
	 * @return the value of $key in the cache
	 * Comments: $expireTime defaults to self::expireTime
	 * 
	 */
	public static function set($key, $value, $expireTime = null){
		$start = microtime(true);
		
		self::$data[$key] = $value;
		$expireTime = ( time() < $expireTime ? $expireTime : time() + $expireTime );
		
		if(!is_null($expireTime))
			self::$expires[$key] = rand($expireTime * 0.8, $expireTime * 1.2);

		self::log($key, null, microtime(true) - $start);

		return $value;
	}
}

?>
