<?php
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Additional utility functions can be added here

