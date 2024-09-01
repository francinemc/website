<?php
// Include database connection
include 'db.php';

// Start session to access session variables
session_start();

// Initialize message variable
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure user_id is available
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        $user_id = null; // or handle it as needed
    }
    
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $feedback = $_POST['feedback'];
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;

    // Validate input
    if (empty($full_name) || empty($email) || empty($feedback) || $user_id === null) {
        $message = "Please fill in all required fields.";
    } else {
        // Prepare and execute the SQL query
        $stmt = $mysqli->prepare("INSERT INTO feedback (user_id, full_name, email, feedback, rating, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt === false) {
            die("Error preparing statement: " . $mysqli->error);
        }
        
        // Bind parameters and execute
        $stmt->bind_param("isssi", $user_id, $full_name, $email, $feedback, $rating);

        if ($stmt->execute()) {
            $message = "Feedback submitted successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Fetch feedback for display
$reviews = [];
if ($result = $mysqli->query("SELECT full_name, feedback, rating, created_at FROM feedback ORDER BY created_at DESC")) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $result->free();
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Tire</title>
    <link rel="stylesheet" href="css/aboutus.css">
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f5f5f5;
            transition: background-color 0.3s ease; /* Smooth background color transition */
        }

        header {
            background: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-bottom: none; /* Removed white lining */
        }

        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease, transform 0.3s ease; /* Smooth shadow and scale transition */
        }

        .container:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Increased shadow on hover */
            transform: translateY(-5px); /* Lift container on hover */
        }

        .mission-vision {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .mission-section, .vision-section {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth color and scale transition */
        }

        .mission-section {
            background-color: #f5f5f5; /* Grayish background for mission */
            color: #333;
        }

        .vision-section {
            background-color: #ff9800; /* Orange background for vision */
            color: #fff;
        }

        .mission-section:hover, .vision-section:hover {
            transform: scale(1.02); /* Slightly scale up on hover */
        }

        .mission-section h2, .vision-section h2 {
            font-size: 1.8em;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .mission-section p, .vision-section p {
            font-size: 1.1em;
            line-height: 1.6;
            margin: 0;
        }

        .feedback-section, .customer-review, .brands-section, .trusted-products-section {
            margin-bottom: 20px;
        }

        .feedback-section h2, .customer-review h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8em;
            color: #333;
        }

        .feedback-section form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 600px;
            margin: 0 auto;
            transition: background-color 0.3s ease; /* Smooth form background color transition */
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            transition: border-color 0.3s ease; /* Smooth border color transition */
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff9800; /* Orange border on focus */
            outline: none;
        }

        button {
            padding: 12px 25px;
            background-color: #ff9800; /* Orange button */
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s; /* Smooth color and scale transition */
        }

        button:hover {
            background-color: #e68900; /* Darker orange on hover */
            transform: scale(1.05); /* Slightly scale up on hover */
        }

        /* Styling for the rating stars */
        .rating-stars {
            direction: rtl;
            display: flex;
            justify-content: center;
            gap: 5px;
            font-size: 1.5rem;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            cursor: pointer;
            color: #ffeb3b; /* Yellow color for rating stars */
            transition: color 0.3s ease; /* Smooth color transition */
        }

        .rating-stars input[type="radio"]:checked ~ label {
            color: #ffeb3b;
        }

        .rating-stars input[type="radio"]:checked ~ input[type="radio"] ~ label {
            color: #ddd;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: green;
        }

        /* Customer review section styles */
        .customer-review {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .carousel {
            position: relative;
            width: 100%;
            max-width: 800px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease; /* Smooth shadow transition */
        }

        .carousel:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Increased shadow on hover */
        }

        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-item {
            flex: 1 0 100%;
            box-sizing: border-box;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 10px;
            text-align: center;
            transition: transform 0.3s ease; /* Smooth scale transition */
        }

        .carousel-item:hover {
            transform: scale(1.03); /* Slightly scale up on hover */
        }

        .carousel-item img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-bottom: 10px;
            transition: transform 0.3s ease; /* Smooth image scale transition */
        }

        .carousel-item:hover img {
            transform: scale(1.1); /* Slightly scale up image on hover */
        }

        .carousel-item blockquote {
            font-size: 1.1em;
            line-height: 1.5;
            margin: 15px 0;
            color: #555;
            font-style: italic;
            padding: 15px;
            border-left: 5px solid #ff9800; /* Orange border for blockquote */
            background: #f4f4f4;
            border-radius: 6px;
        }

        .carousel-item cite {
            display: block;
            font-style: normal;
            font-weight: bold;
            margin-top: 10px;
            color: #333;
        }

        .carousel-item .rating {
            font-size: 1.3em;
            color: #ffeb3b; /* Yellow color for rating stars */
        }

        .carousel-controls {
            display: flex;
            justify-content: space-between;
            position: absolute;
            top: 50%;
            width: 100%;
            transform: translateY(-50%);
            padding: 0 20px;
            box-sizing: border-box;
        }

        .carousel-control {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 1.8em;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.3s; /* Smooth color and scale transition */
        }

        .carousel-control:hover {
            background-color: #222;
            transform: scale(1.1); /* Slightly scale up on hover */
        }

        .brands-section, .trusted-products-section {
            padding: 20px;
            text-align: center;
        }

        .brands-container, .trusted-products-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .brands-container img, .trusted-products-container img {
            max-width: 200px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth scale and shadow transition */
        }

        .brands-container img:hover, .trusted-products-container img:hover {
            transform: scale(1.05); /* Slightly scale up on hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Increased shadow on hover */
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-top: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <header>
        <h1>Prime Tire is Here to Serve You</h1>
    </header>

    <main class="main-content">
        <section class="mission-vision">
            <div class="container mission-section">
                <h2>Our Mission</h2>
                <p>At <span class="highlight">Prime Tire</span>, our mission is to provide top-quality tires and exceptional customer service, ensuring your safety and satisfaction on every journey.</p>
            </div>
            <div class="container vision-section">
                <h2>Our Vision</h2>
                <p>We envision becoming the leading provider of tires, known for our innovation, reliability, and dedication to excellence, driving the future of automotive solutions.</p>
            </div>
        </section>

        <section class="feedback-section">
            <div class="container">
                <h2>Share Your Feedback</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="feedback">Feedback:</label>
                        <textarea id="feedback" name="feedback" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Rating:</label>
                        <div class="rating-stars">
                            <input type="radio" id="star5" name="rating" value="5"><label for="star5">&#9733;</label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
                        </div>
                    </div>
                    <button type="submit">Submit Feedback</button>
                    <?php if (!empty($message)) { echo '<div class="message">' . htmlspecialchars($message) . '</div>'; } ?>
                </form>
            </div>
        </section>

        <section class="customer-review">
            <div class="container">
                <h2>Customer Reviews</h2>
                <div class="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($reviews as $review) { ?>
                            <div class="carousel-item">
                                <img src="default-user.jpg" alt="Customer">
                                <blockquote>
                                    <?php echo htmlspecialchars($review['feedback']); ?>
                                    <cite>- <?php echo htmlspecialchars($review['full_name']); ?></cite>
                                </blockquote>
                                <div class="rating">
                                    <?php for ($i = 0; $i < $review['rating']; $i++) { echo '&#9733;'; } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="carousel-controls">
                        <button class="carousel-control prev">&#10094;</button>
                        <button class="carousel-control next">&#10095;</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="brands-section">
            <div class="container">
                <h2>Our Brands</h2>
                <p>Explore our range of trusted tire brands that offer durability and performance for all your driving needs.</p>
                <div class="brands-container">
                    <img src="brand1.jpg" alt="Brand 1">
                    <img src="brand2.jpg" alt="Brand 2">
                    <img src="brand3.jpg" alt="Brand 3">
                    <!-- Add more brand images as needed -->
                </div>
            </div>
        </section>

        <section class="trusted-products-section">
            <div class="container">
                <h2>Trusted Products</h2>
                <p>Explore our range of trusted tire brands that offer durability and performance for all your driving needs.</p>
                <div class="trusted-products-container">
                    <img src="trusted-product1.jpg" alt="Trusted Product 1">
                    <img src="trusted-product2.jpg" alt="Trusted Product 2">
                    <img src="trusted-product3.jpg" alt="Trusted Product 3">
                    <img src="trusted-product4.jpg" alt="Trusted Product 4">
                    <img src="trusted-product5.jpg" alt="Trusted Product 5">
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Prime Tire. All rights reserved.</p>
    </footer>

    <script>
        // JavaScript for carousel functionality
        document.addEventListener('DOMContentLoaded', () => {
            const prevButton = document.querySelector('.carousel-control.prev');
            const nextButton = document.querySelector('.carousel-control.next');
            const carouselInner = document.querySelector('.carousel-inner');
            let index = 0;

            function updateCarousel() {
                const items = document.querySelectorAll('.carousel-item');
                const totalItems = items.length;
                const itemWidth = items[0].offsetWidth;
                const offset = -index * itemWidth;
                carouselInner.style.transform = `translateX(${offset}px)`;
            }

            prevButton.addEventListener('click', () => {
                const items = document.querySelectorAll('.carousel-item');
                if (index > 0) {
                    index--;
                } else {
                    index = items.length - 1;
                }
                updateCarousel();
            });

            nextButton.addEventListener('click', () => {
                const items = document.querySelectorAll('.carousel-item');
                if (index < items.length - 1) {
                    index++;
                } else {
                    index = 0;
                }
                updateCarousel();
            });

            updateCarousel(); // Initialize position
        });
    </script>
</body>
</html>
