-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2025 at 10:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `available_quantity` int(11) NOT NULL DEFAULT 1,
  `shelf_location` varchar(50) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `isbn`, `publisher`, `publication_year`, `category`, `description`, `quantity`, `available_quantity`, `shelf_location`, `cover_image`, `created_at`, `updated_at`) VALUES
(1, 'To Kill a Mockingbird', 'Harper Lee', '9780061120084', 'HarperCollins', 1960, 'Fiction', 'A novel about racial injustice and moral growth in the American South.', 5, 5, 'FIC-101', 'mockingbird.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(2, '1984', 'George Orwell', '9780451524935', 'Signet Classics', 1949, 'Fiction', 'A dystopian novel about totalitarianism and surveillance.', 8, 6, 'FIC-102', '1984.jpg', '2025-04-04 07:45:02', '2025-04-06 07:36:57'),
(3, 'The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Scribner', 1925, 'Fiction', 'A story of wealth, love, and the American Dream in the Jazz Age.', 6, 4, 'FIC-103', 'gatsby.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(4, 'Pride and Prejudice', 'Jane Austen', '9780141439518', 'Penguin Classics', 1813, 'Fiction', 'A romantic novel about the Bennet family and their five unmarried daughters.', 7, 7, 'FIC-104', 'pride.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(5, 'The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 'Little, Brown', 1951, 'Fiction', 'A story about teenage alienation and loss of innocence.', 4, 2, 'FIC-105', 'catcher.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(6, 'The Hobbit', 'J.R.R. Tolkien', '9780547928227', 'Houghton Mifflin', 1937, 'Fantasy', 'A fantasy novel about Bilbo Baggins and his adventurous quest.', 10, 8, 'FIC-106', 'hobbit.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(7, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', '9780590353427', 'Scholastic', 1997, 'Fantasy', 'The first book in the Harry Potter series about a young wizard.', 12, 10, 'FIC-107', 'harry1.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(8, 'The Alchemist', 'Paulo Coelho', '9780061122415', 'HarperOne', 1988, 'Fiction', 'A philosophical novel about a shepherd boy\'s journey to find treasure.', 9, 7, 'FIC-108', 'alchemist.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(9, 'The Book Thief', 'Markus Zusak', '9780375831003', 'Knopf', 2005, 'Historical Fiction', 'A story about a girl who steals books in Nazi Germany.', 6, 4, 'FIC-109', 'bookthief.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(10, 'The Kite Runner', 'Khaled Hosseini', '9781594631931', 'Riverhead Books', 2003, 'Fiction', 'A story of friendship and redemption in Afghanistan.', 5, 3, 'FIC-110', 'kite.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(11, 'The Da Vinci Code', 'Dan Brown', '9780307474278', 'Doubleday', 2003, 'Thriller', 'A mystery thriller about a conspiracy within Christianity.', 8, 6, 'FIC-111', 'davinci.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(12, 'Gone Girl', 'Gillian Flynn', '9780307588364', 'Crown', 2012, 'Thriller', 'A psychological thriller about a missing wife.', 7, 4, 'FIC-112', 'gonegirl.jpg', '2025-04-04 07:45:02', '2025-04-05 16:38:19'),
(13, 'The Girl on the Train', 'Paula Hawkins', '9781594634024', 'Riverhead Books', 2015, 'Thriller', 'A psychological thriller about a woman who becomes entangled in a missing person investigation.', 6, 4, 'FIC-113', 'girltrain.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(14, 'The Silent Patient', 'Alex Michaelides', '9781250301697', 'Celadon Books', 2019, 'Thriller', 'A psychological thriller about a woman who shoots her husband and then stops speaking.', 5, 3, 'FIC-114', 'silentpatient.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(15, 'Where the Crawdads Sing', 'Delia Owens', '9780735219090', 'G.P. Putnam\'s Sons', 2018, 'Fiction', 'A novel about an abandoned girl who raises herself in the marshes of North Carolina.', 7, 5, 'FIC-115', 'crawdads.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(16, 'Little Fires Everywhere', 'Celeste Ng', '9780735224292', 'Penguin Press', 2017, 'Fiction', 'A novel about family, secrets, and the weight of motherhood.', 6, 4, 'FIC-116', 'littlefires.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(17, 'The Nightingale', 'Kristin Hannah', '9780312577223', 'St. Martin\'s Press', 2015, 'Historical Fiction', 'A story about two sisters in France during WWII.', 5, 3, 'FIC-117', 'nightingale.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(18, 'Educated', 'Tara Westover', '9780399590504', 'Random House', 2018, 'Memoir', 'A memoir about a woman who leaves her survivalist family and goes on to earn a PhD.', 6, 4, 'FIC-118', 'educated.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(19, 'Becoming', 'Michelle Obama', '9781524763138', 'Crown', 2018, 'Memoir', 'A memoir by the former First Lady of the United States.', 8, 6, 'FIC-119', 'becoming.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(20, 'The Testaments', 'Margaret Atwood', '9780385543781', 'Nan A. Talese', 2019, 'Dystopian', 'The sequel to The Handmaid\'s Tale, set 15 years after the first novel.', 5, 3, 'FIC-120', 'testaments.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(21, 'A Brief History of Time', 'Stephen Hawking', '9780553109535', 'Bantam', 1988, 'Science', 'A popular-science book on cosmology.', 6, 4, 'SCI-201', 'time.jpg', '2025-04-04 07:45:02', '2025-04-06 07:36:53'),
(22, 'The Selfish Gene', 'Richard Dawkins', '9780199291151', 'Oxford University Press', 1976, 'Science', 'A book about evolution from the gene\'s perspective.', 5, 3, 'SCI-202', 'selfishgene.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(23, 'Cosmos', 'Carl Sagan', '9780345539434', 'Random House', 1980, 'Science', 'A book about the universe and human understanding of it.', 7, 5, 'SCI-203', 'cosmos.jpg', '2025-04-04 07:45:02', '2025-04-05 06:57:47'),
(24, 'The Double Helix', 'James D. Watson', '9780743216302', 'Simon & Schuster', 1968, 'Science', 'An account of the discovery of the structure of DNA.', 4, 2, 'SCI-204', 'doublehelix.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(25, 'The Elegant Universe', 'Brian Greene', '9780375708114', 'W.W. Norton', 1999, 'Science', 'An introduction to string theory and quantum mechanics.', 5, 3, 'SCI-205', 'elegant.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(26, 'The Immortal Life of Henrietta Lacks', 'Rebecca Skloot', '9781400052189', 'Crown', 2010, 'Science', 'The story of Henrietta Lacks and the immortal cell line taken from her.', 6, 4, 'SCI-206', 'henrietta.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(27, 'The Gene: An Intimate History', 'Siddhartha Mukherjee', '9781476733500', 'Scribner', 2016, 'Science', 'A history of the gene and genetics research.', 5, 3, 'SCI-207', 'gene.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(28, 'Astrophysics for People in a Hurry', 'Neil deGrasse Tyson', '9780393609394', 'W.W. Norton', 2017, 'Science', 'An introduction to astrophysics concepts.', 7, 5, 'SCI-208', 'astrophysics.jpg', '2025-04-04 07:45:02', '2025-04-04 18:34:55'),
(29, 'The Emperor of All Maladies', 'Siddhartha Mukherjee', '9781439107959', 'Scribner', 2010, 'Science', 'A biography of cancer.', 6, 4, 'SCI-209', 'cancer.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(30, 'The Sixth Extinction', 'Elizabeth Kolbert', '9780805092998', 'Henry Holt', 2014, 'Science', 'A book about human impact on biodiversity.', 5, 3, 'SCI-210', 'extinction.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(31, 'Clean Code', 'Robert C. Martin', '9780132350884', 'Prentice Hall', 2008, 'Technology', 'A handbook of agile software craftsmanship.', 8, 6, 'TECH-301', 'cleancode.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(32, 'The Pragmatic Programmer', 'Andrew Hunt', '9780201616224', 'Addison-Wesley', 1999, 'Technology', 'A guide to better programming practices.', 7, 5, 'TECH-302', 'pragmatic.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(33, 'Design Patterns', 'Erich Gamma', '9780201633610', 'Addison-Wesley', 1994, 'Technology', 'Elements of reusable object-oriented software.', 6, 4, 'TECH-303', 'patterns.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(35, 'The Mythical Man-Month', 'Fred Brooks', '9780201835953', 'Addison-Wesley', 1975, 'Technology', 'Essays on software engineering.', 4, 2, 'TECH-305', 'manmonth.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(36, 'Artificial Intelligence: A Modern Approach', 'Stuart Russell', '9780136042594', 'Pearson', 2009, 'Technology', 'A comprehensive textbook on AI.', 7, 5, 'TECH-306', 'ai.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(37, 'Introduction to Algorithms', 'Thomas H. Cormen', '9780262033848', 'MIT Press', 2009, 'Technology', 'A comprehensive textbook on algorithms.', 6, 4, 'TECH-307', 'algorithms.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(38, 'The Soul of a New Machine', 'Tracy Kidder', '9780316491976', 'Little, Brown', 1981, 'Technology', 'A story of computer engineers building a new machine.', 5, 3, 'TECH-308', 'newmachine.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(39, 'Hackers: Heroes of the Computer Revolution', 'Steven Levy', '9781449388393', 'O\'Reilly', 1984, 'Technology', 'A history of the hacker culture.', 4, 2, 'TECH-309', 'hackers.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(40, 'The Innovators', 'Walter Isaacson', '9781476708690', 'Simon & Schuster', 2014, 'Technology', 'A history of the digital revolution.', 6, 4, 'TECH-310', 'innovators.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(41, 'The Lean Startup', 'Eric Ries', '9780307887894', 'Crown Business', 2011, 'Business', 'A new approach to business that\'s being adopted around the world.', 7, 5, 'BUS-401', 'lean.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(42, 'Good to Great', 'Jim Collins', '9780066620992', 'HarperBusiness', 2001, 'Business', 'Why some companies make the leap... and others don\'t.', 6, 4, 'BUS-402', 'goodtogreat.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(43, 'The 7 Habits of Highly Effective People', 'Stephen R. Covey', '9780743269513', 'Free Press', 1989, 'Business', 'A business and self-help book.', 8, 6, 'BUS-403', '7habits.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(44, 'Thinking, Fast and Slow', 'Daniel Kahneman', '9780374533557', 'Farrar, Straus and Giroux', 2011, 'Business', 'A book about behavioral economics and decision-making.', 5, 3, 'BUS-404', 'thinking.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(45, 'The Hard Thing About Hard Things', 'Ben Horowitz', '9780062273208', 'HarperBusiness', 2014, 'Business', 'Building a business when there are no easy answers.', 6, 4, 'BUS-405', 'hardthings.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(46, 'Sapiens', 'Yuval Noah Harari', '9780062316097', 'Harper', 2014, 'History', 'A brief history of humankind.', 8, 6, 'HIST-501', 'sapiens.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(47, 'Guns, Germs, and Steel', 'Jared Diamond', '9780393317558', 'W.W. Norton', 1997, 'History', 'The fates of human societies.', 7, 5, 'HIST-502', 'guns.jpg', '2025-04-04 07:45:02', '2025-04-04 18:04:17'),
(48, 'The Wright Brothers', 'David McCullough', '9781476728742', 'Simon & Schuster', 2015, 'History', 'The story of the brothers who invented the airplane.', 6, 4, 'HIST-503', 'wright.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(49, '1776', 'David McCullough', '9780743226721', 'Simon & Schuster', 2005, 'History', 'The story of America\'s founding year.', 5, 3, 'HIST-504', '1776.jpg', '2025-04-04 07:45:02', '2025-04-06 07:36:47'),
(50, 'The Warmth of Other Suns', 'Isabel Wilkerson', '9780679444329', 'Random House', 2010, 'History', 'The epic story of America\'s great migration.', 6, 4, 'HIST-505', 'warmth.jpg', '2025-04-04 07:45:02', '2025-04-04 07:45:02'),
(282, 'Code Complete', 'Steve McConnell', '9780735619679', 'Microsoft Press', 2004, 'Technology', 'A guide to writing high-quality code, focusing on coding techniques and principles.', 7, 7, 'E2', 'cover_code_complete.jpg', '2025-04-05 18:50:08', '2025-04-05 18:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `fine_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `status` enum('pending','fulfilled','cancelled') NOT NULL DEFAULT 'pending',
  `queue_position` int(11) DEFAULT NULL,
  `notified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `book_id`, `user_id`, `review_text`, `rating`, `review_date`) VALUES
(1, 1, 22, 'excelent', 5, '2025-04-05 23:20:07'),
(4, 1, 22, 'good', 3, '2025-04-05 23:27:49');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('issued','returned','overdue') NOT NULL DEFAULT 'issued',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `book_id`, `user_id`, `issue_date`, `due_date`, `return_date`, `fine_amount`, `status`, `created_at`, `updated_at`) VALUES
(21, 2, 1, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:28:37', '2025-04-04 09:29:12'),
(22, 2, 1, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:39:07', '2025-04-04 18:04:22'),
(23, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:44:35', '2025-04-04 14:11:33'),
(24, 21, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:44:43', '2025-04-04 14:11:37'),
(25, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:44:51', '2025-04-04 14:11:40'),
(26, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:45:11', '2025-04-04 14:20:57'),
(27, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:45:21', '2025-04-04 14:21:01'),
(28, 21, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:45:45', '2025-04-04 14:21:04'),
(29, 47, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:45:56', '2025-04-04 18:04:17'),
(30, 49, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:49:39', '2025-04-04 18:04:12'),
(31, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 09:53:51', '2025-04-04 18:04:08'),
(32, 49, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 13:56:01', '2025-04-04 18:04:04'),
(33, 21, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 13:56:15', '2025-04-04 18:03:57'),
(34, 21, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 13:57:07', '2025-04-04 18:03:53'),
(35, 49, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 13:57:18', '2025-04-04 18:03:47'),
(36, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 14:19:14', '2025-04-04 18:03:37'),
(37, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 17:46:40', '2025-04-04 18:04:25'),
(38, 28, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 18:10:33', '2025-04-04 18:34:55'),
(39, 2, 22, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 18:10:36', '2025-04-04 18:35:00'),
(40, 2, 24, '2025-04-04', '2025-04-18', '2025-04-04', 0.00, 'returned', '2025-04-04 18:34:20', '2025-04-04 18:35:12'),
(41, 2, 22, '2025-04-04', '2025-04-18', '2025-04-05', 0.00, 'returned', '2025-04-04 18:36:22', '2025-04-05 06:57:32'),
(42, 23, 22, '2025-04-04', '2025-04-18', '2025-04-05', 0.00, 'returned', '2025-04-04 18:36:37', '2025-04-05 06:57:47'),
(43, 49, 22, '2025-04-05', '2025-04-19', '2025-04-05', 0.00, 'returned', '2025-04-05 07:29:36', '2025-04-05 07:48:51'),
(44, 2, 22, '2025-04-05', '2025-04-19', '2025-04-05', 0.00, 'returned', '2025-04-05 07:29:57', '2025-04-05 09:49:34'),
(45, 2, 22, '2025-04-05', '2025-04-19', '2025-04-05', 0.00, 'returned', '2025-04-05 07:30:06', '2025-04-05 09:49:38'),
(46, 2, 22, '2025-04-05', '2025-04-19', '2025-04-05', 0.00, 'returned', '2025-04-05 07:36:06', '2025-04-05 09:49:41'),
(47, 2, 22, '2025-04-05', '2025-04-19', '2025-04-05', 0.00, 'returned', '2025-04-05 07:36:13', '2025-04-05 09:49:44'),
(48, 2, 22, '2025-04-05', '2025-04-19', '2025-04-06', 0.00, 'returned', '2025-04-05 07:48:58', '2025-04-06 07:35:50'),
(49, 2, 22, '2025-04-05', '2025-04-19', '2025-04-06', 0.00, 'returned', '2025-04-05 07:51:24', '2025-04-06 07:35:54'),
(50, 49, 22, '2025-04-05', '2025-04-19', '2025-04-06', 0.00, 'returned', '2025-04-05 08:39:04', '2025-04-06 07:35:57'),
(51, 49, 22, '2025-04-05', '2025-04-19', '2025-04-06', 0.00, 'returned', '2025-04-05 08:44:54', '2025-04-06 07:36:43'),
(52, 49, 22, '2025-04-05', '2025-04-19', '2025-04-06', 0.00, 'returned', '2025-04-05 09:03:41', '2025-04-06 07:36:47'),
(53, 21, 22, '2025-04-05', '2025-04-19', '2025-04-06', 0.00, 'returned', '2025-04-05 09:03:51', '2025-04-06 07:36:53'),
(54, 21, 22, '2025-04-05', '2025-04-19', '2025-04-05', 0.00, 'returned', '2025-04-05 09:03:57', '2025-04-05 16:16:44'),
(55, 12, 20, '2025-04-05', '2025-04-19', NULL, 0.00, 'issued', '2025-04-05 16:38:19', '2025-04-05 16:38:19'),
(56, 2, 22, '2025-04-06', '2025-04-20', '2025-04-06', 0.00, 'returned', '2025-04-06 06:11:00', '2025-04-06 07:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_type` enum('admin','librarian','member') NOT NULL DEFAULT 'member',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `phone`, `address`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'dilmi2', '$2y$10$sLAqVALWFngZIoBoKcp0vefgQ3OeSnTMV8KqV6MjNeROnXWucaV1.', 'hi@gmail.com', 'dilmi', '+94 775765299', '12345', 'member', 'active', '2025-04-04 07:38:08', '2025-04-04 17:19:52'),
(13, 'librarian1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian1@library.com', 'Sarah Johnson', '555-0202', '456 Library Ave', 'librarian', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(14, 'librarian2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian2@library.com', 'Michael Chen', '555-0303', '789 Book Lane', 'librarian', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(15, 'member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member1@email.com', 'Emily Wilson', '555-0404', '101 Reader Rd', 'member', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(16, 'member2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member2@email.com', 'David Brown', '555-0505', '202 Bookworm St', 'member', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(17, 'member3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member3@email.com', 'Jessica Lee', '555-0606', '303 Chapter Ave', 'member', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(18, 'member4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member4@email.com', 'Robert Garcia', '555-0707', '404 Page Dr', 'member', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(19, 'member5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member5@email.com', 'Amanda Taylor', '555-0808', '505 Novel Way', 'member', 'active', '2025-04-04 07:53:31', '2025-04-05 06:51:14'),
(20, 'member6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member6@email.com', 'Daniel Martinez', '555-0909', '606 Story Blvd', 'member', 'active', '2025-04-04 07:53:31', '2025-04-04 07:53:31'),
(21, 'member7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member7@email.com', 'Jennifer Davis', '555-1010', '707 Literature Ct', 'member', 'active', '2025-04-04 07:53:31', '2025-04-05 06:51:29'),
(22, 'dilmi', '$2y$10$ehoUIg7Z1FFaygSPVaFQDuXJmiCHAwKl7lethnU.Rl9JOpxqEt9Fy', 'hello@gmail.com', 'dilmi', NULL, NULL, 'member', 'active', '2025-04-04 09:44:12', '2025-04-04 09:44:12'),
(24, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@library.com', 'Library Admin', NULL, NULL, 'admin', 'active', '2025-04-04 17:20:06', '2025-04-04 17:20:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`fine_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fine_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fines_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
