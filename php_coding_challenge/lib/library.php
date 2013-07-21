<?

/**
 * A library object stores a set of books.  It is managed by a librarian.
 */

class Library{

	var $books;

	/**
	 * Constructor for this library
	 */
	public function __construct(){
		$this->books = array();
	}

	/**
	 * adds a book to the library
	 * @param book $book a book to be added to the lib
	 */
	public function add($book){
		$this->books[] = $book;
	}

	/**
	 * @param string $title a title of a book.
	 * @return  array of the indices of the book in the library.  if not in the library, return empty array;
	 */
	public function findBookIndicesByTitle($title){
		return array_keys(array_filter($this->books, function($book) use ($title){
			return $book->get('title') == $title;
		}));
	}

	/**
	 * @return  array index of all books that are unread.
	 */
	public function findUnreadBooks(){
		return array_keys(array_filter($this->books, function($book){
			return $book->get('isRead') == false;
		}));
	}

	/**
	 * @param string $author an author of a book.
	 * @return  array an array of the indexes of the book in the library.  if not in the library, return empty array;
	 */
	public function findBookIndicesByAuthor($author){
		return array_keys(array_filter($this->books, function($book) use ($author){
			return $book->get('author') == $author;
		}));
	}
	/**
	 * @param  array $indices an array of book indices
	 * @return an array of books that are only in the indices passed into this function.
	 */
	public function getBooksByIndices($indices){
		$returnBooks = array();
		foreach($indices as $i){
			if($this->books[$i])
				$returnBooks[] = $this->books[$i];
		}
		return $returnBooks;
	}

}

?>