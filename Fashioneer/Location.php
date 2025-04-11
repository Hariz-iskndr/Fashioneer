<!DOCTYPE html>

<html lang="en">
    <head>
        <link href="Fashioneer.css" rel="stylesheet" type="text/css">
        <title>Fashioneer</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <nav>
            <div id="navbar">
                <div id="navbar-logo">
                    <a href="Home.php"><img src="Fashioneer.png" alt="Logo" width="300" height="50"></a>
                </div>
                <div id="navbar-links">
                    <ul>
                        <li>
                            <a href="About.php">About Us</a>
                        </li>
                        <li>
                            <a href="Men.php">Shop</a>
                        </li>
                        <li>
                            <a href="Location.php">Location</a>
                        </li>
                        <li>
                            <a href="Contact.php">Contact Us</a>
                        </li>
                    </ul>
                    
                </div>
                <button class="Button-Fill">
                    <a href="Admin.php">Admin</a>
                </button>
            </div>
        </nav>

        <div id="top-section">
            <div id="top-section-wrapper">
                <div id="top-section-text">
                    <h1>
                        Our Locations  
                    </h1>
                </div>
            </div>
        </div>    

        <div class="location-container">
            <a href="https://www.google.com/maps/search/?api=1&query=Kuala+Lumpur+Malaysia" class="location-item" target="_blank">
                <div class="location-image">
                    <img src="KualaLumpur.jpg" alt="Kuala Lumpur Store">
                </div>
                <div class="location-text">
                    <h2>Kuala Lumpur</h2>
                    <p>Visit our flagship store in the heart of Kuala Lumpur, where fashion meets innovation.</p>
                </div>
            </a>

            <a href="https://www.google.com/maps/search/?api=1&query=Johor+Bahru+Malaysia" class="location-item" target="_blank">
                <div class="location-image">
                    <img src="Johor.jpg" alt="Johor Store">
                </div>
                <div class="location-text">
                    <h2>Johor</h2>
                    <p>Experience our unique collection at our Johor location, bringing style to the southern region.</p>
                </div>
            </a>

            <a href="https://www.google.com/maps/search/?api=1&query=Kuching+Sarawak+Malaysia" class="location-item" target="_blank">
                <div class="location-image">
                    <img src="Sarawak.jpg" alt="Sarawak Store">
                </div>
                <div class="location-text">
                    <h2>Sarawak</h2>
                    <p>Discover our latest trends at our Sarawak store, serving the eastern region with premium fashion.</p>
                </div>
            </a>
        </div>
    </body> 
</html> 