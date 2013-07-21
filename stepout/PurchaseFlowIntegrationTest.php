<?

require_once 'Tests/PHPUnit/WebDriverTest.php';

/**
 * Test for the Purchase Flow
 * @package Test/Integration
 * @author kevin@stepout.com
 */
class PurchaseIntegrationFlowTest extends WebDriverTest {

	/**
	 * Tests that user can get to the order summary page through clicking on chat list
	 * @group Integration
	 * @group Integration/Purchases
	 * @group Integration/Smoke
	 */
	public function testOrderSummaryPage(){
		$from = $me = new User(self::createUser());
		$to = $them = new User(self::createUser());

		// Set the recipient online
		$to->setOnlineStatus('online', true);

		$this->url($this->urlHelper('About', null, null, array('session_key' => $from->Session_Key())));
		$this->disableLongPoll();

		try {
			// Select the contact list
			$this->byCssSelector('#chat ul.list_tabs a[rel=nearme]')->click();
		} catch(RuntimeException $e){
			$this->fail('Unable to find Contacts tab');
		}

		try {
			$this->timeouts()->implicitWait(10000);
			$this->byCssSelector("#chat .buddy")->click();
		} catch(RuntimeException $e){
			$this->fail('Buddy not online');
		}

		try {
			$title = $this->byCssSelector('#facebox h1');
			$this->assertTrue((bool)stristr($title->text(), 'Connect with'), 'Facebox h1 has incorrect text');
		} catch(RuntimeException $e){
			$this->fail('Connect Facebox didnt display');
		}

		try {
			$this->byCssSelector('#facebox .subscribe .button')->click();
		} catch(RuntimeException $e){
			$this->fail('Subscribe Now Button not found');
		}

		try {
			$this->byCssSelector('#subscriptions fieldset[data-product-id=SUB1] .button')->click();
		} catch(RuntimeException $e){
			$this->fail('Select Plan Button not found');
		}

		try {
			$payment_method = $this->byCssSelector('#payment_methods label.active');
			$this->assertTrue((bool)stristr($payment_method->text(), 'Credit'), 'Cant find payment method');
		} catch(RuntimeException $e){
			$this->fail("Order Confirmation Page Didn't Display");
		}

	}

	/**
	 * Tests that user can get to 'send a rose' flow
	 * @group Integration
	 * @group Integration/Purchases
	 * @group Integration/Smoke
	 */
	public function testRoseFlow(){
		$from = $me = new User(self::createUser());
		$to = $them = new User(self::createUser());

		// Set the recipient online
		$to->setOnlineStatus('online', true);

		$this->url($this->urlHelper('About', null, null, array('session_key' => $from->Session_Key())));
		$this->disableLongPoll();

		try {
			// Select the contact list
			$this->byCssSelector('#chat ul.list_tabs a[rel=nearme]')->click();
		} catch(RuntimeException $e){
			$this->fail('Unable to find Contacts tab');
		}

		try {
			$this->timeouts()->implicitWait(10000);
			$this->byCssSelector("#chat .buddy")->click();
		} catch(RuntimeException $e){
			$this->fail('Buddy not online');
		}

		try {
			$title = $this->byCssSelector('#facebox h1');
			$this->assertTrue((bool)stristr($title->text(), 'Connect with'), 'Facebox h1 has incorrect text');
		} catch(RuntimeException $e){
			$this->fail('Connect Facebox didnt display');
		}

		try {
			$this->byCssSelector('#facebox .rose .button')->click();
		} catch(RuntimeException $e){
			$this->fail('Send a Rose Button not found');
		}

		try {
			$this->byCssSelector('.upsubmit input')->click();
		} catch(RuntimeException $e){
			$this->fail('Yes I want to Use Points button not found');
		}
	}

	/**
	 * Tests that user can get to 'send an icebreaker' flow
	 * @group Integration
	 * @group Integration/Purchases
	 * @group Integration/Smoke
	 */
	public function testIcebreakerFlow(){
		$from = $me = new User(self::createUser());
		$to = $them = new User(self::createUser());

		// Set the recipient online
		$to->setOnlineStatus('online', true);

		$this->url($this->urlHelper('About', null, null, array('session_key' => $from->Session_Key())));
		$this->disableLongPoll();

		try {
			// Select the contact list
			$this->byCssSelector('#chat ul.list_tabs a[rel=nearme]')->click();
		} catch(RuntimeException $e){
			$this->fail('Unable to find Contacts tab');
		}

		try {
			$this->timeouts()->implicitWait(10000);
			$this->byCssSelector("#chat .buddy")->click();
		} catch(RuntimeException $e){
			$this->fail('Buddy not online');
		}

		try {
			$title = $this->byCssSelector('#facebox h1');
			$this->assertTrue((bool)stristr($title->text(), 'Connect with'), 'Facebox h1 has incorrect text');
		} catch(RuntimeException $e){
			$this->fail('Connect Facebox didnt display');
		}

		try {
			$this->byCssSelector('#facebox .icebreaker .button')->click();
		} catch(RuntimeException $e){
			$this->fail('Send a Rose Button not found');
		}

		try {
			$this->byCssSelector('#icebreaker_send')->click();
		} catch(RuntimeException $e){
			$this->fail('Send icebreaker button not found');
		}
	}

}

?>


