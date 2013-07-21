<?

/**
 * A small system for managing a personal library. The system should is accessible from the command line. 
 * @author  Kevin Owocki <ksowocki@gmail.com>
 */

//load the model && do some prep
include('lib/index.php');
$library = new Library();
$librarian = new Librarian();

//2. go to work
$librarian->workAt($library);

//3. profit!
?>