<?

/**
 * Librarian object is the interface between the end-user and the Library.
 */
class Librarian{

	//a list of available actions to this librarian
	var $availableActions = array('add',
					'read',
					'show all',
					'show unread',
					'show all by',
					'show unread by',
					'quit');
	//is this librarian ready to quit for the day?
	var $readyToQuit = false;
	
	//a speechparser object
	//@see speechparser
	var $speechParser = null;

	//references this librarians library
	var $library = null;

	/**
	 * Constructor for the Librarian class
	 */
	public function __construct(){
		$this->speechParser = new SpeechParser();
	}

	/**
	 * [workAt description]
	 * @param  Library object $library a library for this librarian to work at
	 * @return NULL  -- method only returns once the user has requested that they quit.
	 */
	public function workAt(&$library){
		$this->library = &$library;
		
		$this->speak("Welcome to your library!");

		do{
			$this->speak("> ",false);
			$response = $this->solicit(fgets(STDIN));
			$this->speak($response);

		} while($this->shouldContinueSolicitations());

	}

	/**
	 * @param  string  $sentence a sentence to be outputted
	 * @param  boolean $newLine  should a new line be dispplayed after the sentence
	 */
	public function speak($sentence,$newLine=true){
		echo $sentence;
		if($newLine)
			echo "\n";
	}

	/**
	 * [solicit description]
	 * @param  string $sentence a command to be executed by the librarian
	 * @return string an end-user readable response.
	 */
	public function solicit($sentence){
		try {
			$action = $this->speechParser->getActionItem($sentence);
			$arguments = $this->speechParser->getArguments($sentence);

			$this->validateCommand($action,$arguments);
			$response = $this->executeAction($action,$arguments);
		} catch(LibrarianException $e){
			$errorMessage = $e->getMessage();
			$response = $errorMessage;
		}

		return $response;
	}

	/**
	 * @return boolean is the librarian ready to end soliciting for commands?
	 */
	public function shouldContinueSolicitations(){
		return !$this->readyToQuit;
	}

	/**
	 * @param  string $action    an action to be executed
	 * @param  array $arguments a list of arguments upon which to execute $action
	 */
	private function validateCommand($action,$arguments){

		if(!in_array($action, $this->availableActions))
			throw new LibrarianException('That action is not supported.');

		switch($action){
			case 'add':
				$numExceptedArguments = 2;
				break;
			case 'read':
				$numExceptedArguments = 1;
				break;
			case 'show all':
				$numExceptedArguments = 0;
				break;
			case 'show unread':
				$numExceptedArguments = 0;
				break;
			case 'show all by':
				$numExceptedArguments = 1;
				break;
			case 'show unread by':
				$numExceptedArguments = 1;
				break;
			case 'quit':
				$numExceptedArguments = 0;
				break;
		}

		if(count($arguments)!=$numExceptedArguments)
			throw new LibrarianException('You\'ve provided the wrong number of arguments.');

	}

	/**
	 * Executes the action supplied bye the user.
	 * @param  string $action    an action to be executed
	 * @param  array $arguments a list of arguments upon which to execute $action
	 * @example:
		**add "$title" "$author"**: adds a book to the library with the given title and author. All books are unread by default.
		**read "$title"**: marks a given book as read.
		**show all**: displays all of the books in the library
		**show unread**: display all of the books that are unread
		**show all by "$author"**: shows all of the books in the library by the given author.
		**show unread by "$author"**: shows the unread books in the library by the given author
		**quit**: quits the program.
	 * @todo  there is a little bit of duplicated code in this function.  the function is also pretty large.  probably a good candidate for a little refactoring one day.
	 *
	 * NOTE: I went back and forth debating the architecture of how the librarian interacts with the library.   While it might be more performant and maintainable to have the library do some of the index searching and pulling the books out of the library, I chose to model how the library in my 1980s elementary school worked.  ie. a Librarian would look through a card catalog, get an index number, and then find the books by the index numbers.  A more performant way to do this would be to (clearly) let MySQL handle the SELECT functionality.  In which case, the functionality would largely be owned by a superclass in of the `library` and `book` objects
	 * 
	 */
	private function executeAction($action,$arguments){

		switch($action){
			case 'add':
				$title = $arguments[0];
				$author = $arguments[1];

				//find out if library already has that book
				$numAlreadyCheckedIn = count($this->library->findBookIndicesByTitle($title));
				if($numAlreadyCheckedIn != 0)
					throw new LibrarianException('Sorry, this title is already in the library.');

				//add book
				$book = new Book($title,$author);
				$this->library->add($book);

				//tell the user!
				$this->speak('Added '.$book->description());

				break;
			case 'read':
				$title = $arguments[0];

				//find if we have that book on file
				$bookIndex = array_pop($this->library->findBookIndicesByTitle($title));
				if(!isset($bookIndex) || !isset($this->library->books[$bookIndex]))
					throw new LibrarianException('I\'m sorry, I could not find this book.');

				//mark as read
				$this->library->books[$bookIndex]->read();

				//tell the user!
				$this->speak("You've read '".($this->library->books[$bookIndex]->get('title'))."'!");
				break;
			case 'show all':

				//find out if library already has any books
				if(!count($books = $this->library->books))
					$this->speak("There are no books in the library");
				else {

					//tell user about the books
					foreach($books as $book)
						$this->speak($book->description());
				}
				break;
			case 'show unread':

				//find out if library already has any books that are unread
				$unreadBooksIndices = $this->library->findUnreadBooks();
				if(!count($unreadBooksIndices))
					$this->speak("There are no unread books in the library");
				else {

					//tell user about the books
					foreach($this->library->getBooksByIndices($unreadBooksIndices) as $book){
						$this->speak($book->description());
					}
				}

				break;
			case 'show all by':
				$author = $arguments[0];

				//find out if library already has any books that are from this author
				$authorBookIndices = $this->library->findBookIndicesByAuthor($author);
				if(!count($authorBookIndices))
					$this->speak("There are no books by this author in the library");
				else {

					//tell user about the books
					foreach($this->library->getBooksByIndices($authorBookIndices) as $book){
						$this->speak($book->description());
					}
				}

				break;
			case 'show unread by':
				$author = $arguments[0];

				//find out if library already has any books that are unread from this author
				$authorBookIndices = $this->library->findBookIndicesByAuthor($author);
				$unreadBooksIndices = $this->library->findUnreadBooks();
				$authorUnreadBookIndices = array_intersect($authorBookIndices, $unreadBooksIndices);

				if(!count($authorUnreadBookIndices))
					$this->speak("There are no unread books by this author in the library");
				else {

					//tell user about the books
					foreach($this->library->getBooksByIndices($authorUnreadBookIndices) as $book){
						$this->speak($book->description());
					}
				}

				break;
			case 'quit':

				//say bai!
				$this->speak("Bye!");
				$this->readyToQuit = true;
				break;
		}

	}

}

class LibrarianException extends Exception{};

?>