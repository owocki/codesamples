<?

class SpeechParser{

	/**
	 * @param  string $sentence a sentence provided by the end user
	 * @return the isolated action item string from the sentence
	 */
	public function getActionItem($sentence){
		$sentence = trim($sentence);

		//parse sentence to obtain a string that only contains the action being requested.
		//we can do so by removing any command following the first double quote.
		$locationOfFirstQuoteChar = strpos($sentence, '"');
		if(!$locationOfFirstQuoteChar)
			return $sentence;

		$command = trim(substr($sentence, 0, $locationOfFirstQuoteChar));
		return $command;
	}

	/**
	 * @param  string $sentence a sentence provided by the end user
	 * @return array - a list of arguments to be applied to any action item from this sentence.
	 */
	public function getArguments($sentence){
		$sentence = trim($sentence);
		$locationOfFirstQuoteChar = strpos($sentence, '"');
		if(!$locationOfFirstQuoteChar)
			return array();

		$targetsStr = trim(substr($sentence, $locationOfFirstQuoteChar, strlen($sentence)));
		$targetsArray = array_filter(explode('"', $targetsStr),function($str){
			return strlen(trim($str));
		});
		reindex($targetsArray);
		return $targetsArray;
	}

}

?>