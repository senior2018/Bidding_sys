<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">

    <title>Document</title>
</head>
<body>
<?php include 'nav.php'; ?>

<!-- auction.php -->
<div class="content">
    <h1>Contact Page</h1>
    <!-- Add your auction content here -->
    <div class= "contact-wrapper">
        <div class="contact-form">
            <h2>Send us message</h2>
            <form>
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name">
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email">
                </div>

                <div class="form-group">
                   <textarea name="message" placeholder="Your Meassage"></textarea>
                </div>

                <button type="submit">Send Message</button>
            </form>
        </div>

        <div class="contact-info">
            <h3>Contact Information</h3>
            <p><i class="fas fa-phone"></i>+ 255 756 959 848</p>
            <p><i class="fas fa-envelope"></i>antonykapinga@gmail.com</p>
            <p><i class="fas fa-map-marker-alt"></i>123 Makumbusho, Dar-es-salaam, Tanzania</p>
        </div>
    </div>

</div>
</div>
</section>
</body>
</html>