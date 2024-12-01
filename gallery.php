<?php

include('includes/db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/home.css">
    <title>Gallery - Gym</title>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="index.php" class="btn">Home</a></li>
            <li><a href="about.php" class="btn">About Us</a></li>
            <li><a href="gallery.php" class="cbtn">Gallery</a></li>
            <li><a href="contact.php" class="btn">Contact</a></li>
        </ul>
    </nav>
    <div class="about-container">

        <h2 class="contact-title" style="padding-left:45%;padding-right:45%;padding-top:5px;padding-bottom:5px;font-size: 35px;">Gym Gallery</h2>
        <div class="gallery-grid">

            <div class="gallery-item">
                <img src="assets/images/gym1.jpg" alt="Gym Photo 1" class="gallery-img">
            </div>

            <div class="gallery-item">
                <img src="assets/images/gym2.jpg" alt="Gym Photo 2" class="gallery-img">
            </div>

            <div class="gallery-item">
                <img src="assets/images/gym3.jpg" alt="Gym Photo 3" class="gallery-img">
            </div>

            <div class="gallery-item">
                <img src="assets/images/gym4.jpg" alt="Gym Photo 4" class="gallery-img">
            </div>

            <div class="gallery-item">
                <img src="assets/images/gym5.jpg" alt="Gym Photo 5" class="gallery-img">
            </div>

            <div class="gallery-item">
                <img src="assets/images/gym6.jpg" alt="Gym Photo 6" class="gallery-img">
            </div>
        </div>
    </div>

</body>

</html>