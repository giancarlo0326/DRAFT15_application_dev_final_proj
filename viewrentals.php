<?php
session_start();
require 'database.php';
require 'functions.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch rentals for the logged-in user from the database
$rentals = getUserRentals($conn, $user_id);

// Check if a search query is submitted
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

if (!empty($search_query)) {
    // Perform search
    $videos = searchVideos($conn, $search_query);
}

// Function to search videos based on title or other criteria
function searchVideos($conn, $search_query) {
    $sql = "SELECT * FROM videos WHERE title LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%$search_query%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $videos = [];
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
    return $videos;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - PUIHAHA Videos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        h1 {
            color: #fff;
            font-size: 75px;
            text-align: center;
        }

        .typed-text {
            color: #82420f;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 4rem;
            }
        }

        body {
            position: relative;
        }

        .hero-content {
            position: relative;
            text-align: center;
            margin: 48px, 107.5px, 0px;
        }

        #image-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav>
        <a class="home-link" href="index.php">
            <img src="https://i.postimg.cc/CxLnK8q1/PUIHAHA-VIDEOS.png" alt="Home">
        </a>
        <input type="checkbox" id="sidebar-active">
        <label for="sidebar-active" class="open-sidebar-button">
            <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
        </label>
        <label id="overlay" for="sidebar-active"></label>
        <div class="links-container">
            <label for="sidebar-active" class="close-sidebar-button">
                <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
            </label>
        <a href="add.php">Add Videos</a>
        <a href="videos.php">Videos</a>
        <a href="viewrentals.php">Rentals</a>
        <a href="rent_history.php">Rent History</a>
        <a href="account.php">Account</a>
        <a href="aboutdevs.php">About Us</a>
        <a href="signin.php">Sign In</a>
        <a href="signup.php">Sign Up</a>
        <a href="logout.php">Log Out</a>
        </div>
    </nav>

    <div class="hero-content">
        <h1>My <span class="auto-type typed-text"></span></h1>
        <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
        <script>
            var typed = new Typed(".auto-type", {
                strings: ["Rentals"],
                typeSpeed: 100,
                backSpeed: 10,
            });
        </script>
    </div>

    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="centered-container">
                <div class="row mt-12">
                <div class="col-lg-12 d-flex flex-column flex-lg-row justify-content-between align-items-center">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <form class="form-inline d-flex justify-content-start" method="GET" action="videos.php">
                            <input class="form-control mr-2" type="search" name="search_query" placeholder="Enter title, director, etc..." aria-label="Search">
                            <button class="btn btn-success ml-2" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="col-lg-6 d-flex flex-column flex-lg-row justify-content-end align-items-center">
                        <p class="mb-0 mb-lg-0 mr-lg-2">Sort by: </p>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Video Type
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="videos.php?sort_by=video_type">DVD</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=video_type">Blu-Ray</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=video_type">Digital</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-6 offset-lg-6 d-flex flex-column flex-lg-row justify-content-end align-items-center">
                        <p class="mb-0 mb-lg-0 mr-lg-2"></p>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Release Date
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="videos.php?sort_by=release_date">Newest First</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=release_date&order=asc">Oldest First</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-6 offset-lg-6 d-flex flex-column flex-lg-row justify-content-end align-items-center">
                        <p class="mb-0 mb-lg-0 mr-lg-2"></p>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Genre
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Action</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Comedy</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Drama</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Horror</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Science Fiction</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Fantasy</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Romance</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Thriller</a></li>
                                <li><a class="dropdown-item" href="videos.php?sort_by=genre">Animation</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="container mt-5">
                <div class="row">
                    <?php if (count($rentals) > 0): ?>
                        <?php foreach ($rentals as $rental): ?>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card" style="background-color: #251508; color: white">
                                    <img src="https://knetic.org.uk/wp-content/uploads/2020/07/Video-Placeholder.png" class="img-fluid">
                                    <div class="card-body" style="background-color: #251508; color: white">
                                        <h4 class="card-text"><?php echo htmlspecialchars($rental['title']); ?></h4>
                                        <p class="card-text">Genre: <?php echo htmlspecialchars($rental['genre']); ?></p>
                                        <p class="card-text">Director: <?php echo htmlspecialchars($rental['director']); ?></p>
                                        <form action="return_video.php" method="post" style="display:inline;">
                                            <input type="hidden" name="rental_id" value="<?php echo htmlspecialchars($rental['rental_id']); ?>">
                                            <button type="submit" class="btn btn-danger">Return</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-lg-12">
                            <div class="alert alert-info" role="alert">
                                You have no current rentals.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>


    

    <footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="collab">
                    <img src="https://i.postimg.cc/CxLnK8q1/PUIHAHA-VIDEOS.png" class="collab-img img-fluid">
                </div>
            </div>
            <div class="col-md-4">
                <div class="footerBottom text-center text-md-end">
                    <h3>Application Development and Emerging Technologies - Final Project</h3>
                    <p>This website is for educational purposes only and no copyright infringement is intended.</p>
                    <p>Copyright &copy;2024; All images used in this website are from the Internet.</p>
                    <p>Designed by PIPOPIP, June 2024.</p>
                </div>
            </div>
        </div>
    </div>
</footer>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBogGzOgHU6FQd8cdIYnKio39uA85Fw+8RXbR6p5y5A1tmB4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-Q6EHPGL6XHpFD5Q4l8I6y+Xk2Ay/KDQz3ByYu60g6yuHaY/5XV5C/J5qIQXNUiIt" crossorigin="anonymous"></script>
</body>
</html>
