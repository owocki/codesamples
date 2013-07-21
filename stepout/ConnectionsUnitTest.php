<?

/**
 * Tests the connections fuinctionality
 * @package Test/Library/Helpers
 * @author Kevin
 */
class ConnectionsUnitTest extends StepOutTest {

	var $emptyUser = NULL;

	/**
	 * Setup Method - run when class begins executing.
	 */
	public function SetUp(){
		$this->emptyUser = new User(self::createUser());;
	}
	/**
	 * TearDown Method - run when class ends executing.
	 */
	public function TearDown(){
		unset($this->emptyUser);
	}
	/**
	 * Test that a connection can be added
	 * @group Core
	 * @group Core/connections
	 */
	public function testAddConnection(){
		$user1 = new User(self::createUser());
		$user2 = new User(self::createUser());
		
		$user1->addConnection($user2->ID);
		if(!$user1->isConnection($user2->ID))
			$this->fail("isConnection 1 returned false");
		if(!$user2->isConnection($user1->ID))
			$this->fail("isConnection 2 returned false");

	}

	/**
	 * Test that a connection can be removed
	 * @group Core
	 * @group Core/connections
	 */
	public function testRemoveConnections(){
		$user1 = new User(self::createUser());
		$user2 = new User(self::createUser());
		$user3 = new User(self::createUser());
		
		$user1->addConnection($user2->ID);
		$user1->addConnection($user3->ID);
		$user1->removeConnections();
		
		if(count($user1->connections()) != 0)
			$this->fail("connections not removed");


	}

	/**
	 * Test for invalid input
	 * @group Core
	 * @group Core/connections
	 */
	public function testInvalidInput_NonCommitedUser(){

		$user1 = $this->emptyUser;
		$user2 = $this->emptyUser;
		
		try {
			$user1->addConnection($user2->ID);
		} catch (InvalidArgumentException $e){
			$this->passs();
		}
		$this->fail('Invalid Argument Exception not caught');

	}	


	/**
	 * Test for invalid input, where user1 and user2 are equal
	 * @group Core
	 * @group Core/connections
	 */
	public function testInvalidInput_CantAddSameUser(){

		$user1 = new User(self::createUser());
		$user2 = $user2;
		
		try {
			$user1->addConnection($user2->ID);
		} catch (InvalidArgumentException $e){
			$this->passs();
		}
		$this->fail('Invalid Argument Exception not caught');

	}	


	/**
	 * Test for invalid input - non user object
	 * @group Core
	 * @group Core/connections
	 */
	public function testInvalidInput_NotAUser(){

		$user1 = new User(self::createUser());
		$user2 = 'foo';
		
		try {
			$user1->addConnection($user2->ID);
		} catch (InvalidArgumentException $e){
			$this->passs();
		}
		$this->fail('Invalid Argument Exception not caught');

	}	




}

?>
