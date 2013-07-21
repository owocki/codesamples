<?
/**
  * An UserCard object represents a credit card token that has been stored for recurring billling.
  * @package Library/DataObjects
  * @see  https://www.wrike.com/open.htm?id=9681075
  * @author Kevin
*/


class UserCard extends DataObject  { 
	public $Table = "UserCard";
	public static $name = "UserCard";

	/**
	 * @param int $userID a user id
	 * @param  int $gatewayID a payment gateway id
	 * @return int a usercard ID, which is the users primary card ID
	 */
	public static function getPrimaryID($userID,$gatewayID = null){
		
		$lookupArgs = array(
			'Primary' => '1',
			'UserID' => $userID
			);
		if(!is_null($gatewayID))
			$lookupArgs['GatewayID'] = $gatewayID;

		$IDs = self::lookup($lookupArgs,'ID');
		return $IDs[0];

	}
	/**
	 * adds a card to a users account
	 * @param associative array $fieldValues - the values to set on the card object
	 * @return  int  a usercard ID
	 */
	public static function add( $fieldValues ){

		if(!$fieldValues['UserID'])
			return false;

		if($fieldValues['Primary']){
			self::removeAllPrimary($fieldValues['UserID']);
		}

		$UserCard = new UserCard();
		$UserCard->SetValues($fieldValues);

		return $UserCard->Save();

	}

	/**
	 * removes all primary cards from the user
	 * @param  int $userID the users ID
	 */
	private static function removeAllPrimary($userID){

		$IDS = self::lookup(array(
			'UserID' => $userID,
			'Primary' => "1"
			),'ID');

		foreach(UserCard::DataObjects($IDS) as $userCard){
			$userCard->Fields("Primary")->SetValue(0);
			$userCard->Save();
		}
	}


} /* end object definition */
?>
