                                                                     
                                                                     
                                                                     
                                             
I want you to write a small system for managing a personal library. The system should be accessible from the command line. I would interact with it like so:

	$ ./library
	
	Welcome to your library!
	
	> add "The Grapes of Wrath" "John Steinbeck"
	
	Added "The Grapes of Wrath" by John Steinbeck
	
	> add "Of Mice and Men" "John Steinbeck"
	
	Added "Of Mice and Men" by John Steinbeck
	
	> add "Moby Dick" "Herman Melville"
	
	Added "Moby Dick" by Herman Melville
	
	> show all
	
	"The Grapes of Wrath" by John Steinbeck (unread)
	"Of Mice and Men" by John Steinbeck (unread)
	"Moby Dick" by Herman Melville (unread)
	
	> read "Moby Dick"
	
	You've read "Moby Dick!"
	
	> read "Of Mice and Men"
	
	You've read "Of Mice and Men"!
	
	> show all
	
	"The Grapes of Wrath" by John Steinbeck (unread)
	"Of Mice and Men" by John Steinbeck (read)
	"Moby Dick" by Herman Melville (read)
	
	> show unread
	
	"The Grapes of Wrath" by John Steinbeck (unread)
	
	> show all by "John Steinbeck"
	
	"The Grapes of Wrath" by John Steinbeck (unread)
	"Of Mice and Men" by John Steinbeck (read)
	
	> show unread by "John Steinbeck"
	
	"The Grapes of Wrath" by John Steinbeck (unread)
	
	> quit
	
	Bye!
	
	$

--------------------------

As shown above, the program should accept the following commands:

- **add "$title" "$author"**: adds a book to the library with the given title and author. All books are unread by default.
- **read "$title"**: marks a given book as read.
- **show all**: displays all of the books in the library
- **show unread**: display all of the books that are unread
- **show all by "$author"**: shows all of the books in the library by the given author.
- **show unread by "$author"**: shows the unread books in the library by the given author
- **quit**: quits the program.


Some other stipulations:

- You can use whatever language you want.
- Assume that there can never be two books with the same title in the system (even if they were to have different authors). The user shouldn't be allowed to add two books with the same title.
- **Do not** use a persistance mechanism (ie, a SQL database) for the books. Store them in memory. That is, every time you run the program, the list of books should be empty. Using a database can make some aspects of this a little too easy :)