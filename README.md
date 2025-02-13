# Movie Reviews Website

## Overview
This project is a **Movie Review Web Application** built with **PHP, MySQL, and Bootstrap**. Users can **sign up, log in, view movies, submit reviews, and add favorites**. The project follows a **dark-themed UI** with a smooth and interactive design.
## Movie Reviews Website
![Homepage Screenshot](https://github.com/VictorCraciunas/IMDB---copy/blob/main/images/menu.png?raw=true)
![Login Page](https://github.com/VictorCraciunas/IMDB---copy/blob/main/images/Login.png?raw=true)
![View Movies](https://github.com/VictorCraciunas/IMDB---copy/blob/main/images/view_movies.png?raw=true)
![User Profile](https://github.com/VictorCraciunas/IMDB---copy/blob/main/images/user_profile.png?raw=true)
## Features
- **User Authentication** (Signup, Login, Session Management)
- **Movie Database** (View, Add, Edit Movies)
- **User Reviews** (Submit and View Movie Reviews)
- **Favorites System** (Users Can Mark Movies as Favorites)
- **Responsive UI with Dark Theme**

## Technologies Used
- **Frontend:** HTML, CSS (`styles.css`), Bootstrap
- **Backend:** PHP (`signup.php`, `login.php`, `view_movies.php`, etc.)
- **Database:** MySQL (`moviereviews` DB)

## Setup Instructions
### 1. Install XAMPP (or any Apache+MySQL server)
Download and install [XAMPP](https://www.apachefriends.org/download.html), then **start Apache & MySQL**.

### 2. Import the Database
- Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
- Create a new database: `moviereviews`
- Import the provided SQL file (`database.sql` if available)

### 3. Configure Database Connection
Modify **`connection.php`** to match your MySQL configuration:
```php
$host = "localhost";
$username = "root";
$password = "";
$database = "moviereviews";
$conn = new mysqli($host, $username, $password, $database, 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

### 4. Run the Project
- Move the project folder to `C:/xampp/htdocs/`
- Open a browser and visit:
  ```
  http://localhost/your-project-folder/
  ```

## File Structure
```
/moviereviews
│── config.php             # Loads CSS & JS files
│── connection.php         # Database connection
│── login.php              # User authentication
│── signup.php             # User registration
│── view_movies.php        # Fetch and display movies
│── view_reviews.php       # Fetch and display reviews
│── add_movie.php          # Add new movies
│── add_review.php         # Submit movie reviews
│── add_to_favorites.php   # Mark movies as favorites
│── styles.css             # Custom styles (Dark Theme UI)
│── menu.php               # Navigation menu
│── movie_details.php      # Show movie details
│── process_movie.php      # Handle movie operations
│── test.php               # Test file (for debugging)
```

## Security Considerations
### ** SQL Injection Prevention**
All database interactions should use **prepared statements**:
```php
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
```

### ** Password Hashing (Signup)**
Ensure passwords are stored securely:
```php
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);
$stmt->execute();
```




