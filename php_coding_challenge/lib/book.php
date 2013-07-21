<?

/**
 * A book object, which can simply be stored in a library.
 */

class Book{

	var $title;
	var $author;
	var $isRead;

	/**
	 * @param string $title the books title
	 * @param string $author the books author
	 */
	public function __construct($title,$author){
		$this->title = $title;
		$this->author = $author;
		$this->isRead = false;
	}

	/**
	 * Marks this book as read.
	 */
	public function read(){
		$this->isRead = true;
	}

	/**
	 * @param string $field the name of the field to return off of this object
	 * @param  mixed  the value of that field, null if field not found
	 */
	public function get($field){
		if(!isset($this->$field))
			return null;

		return $this->$field;
	}

	/**
	 * @return string a description of the book
	 */
	public function description(){
		return '"'.$this->title.'" by "'.$this->author.'" ('.($this->isRead ? 'read' : 'unread').")";
	}

}

?>