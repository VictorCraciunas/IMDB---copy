<!-- config.php -->
<?php

$cssFiles = [
    "styles.css",
    "https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
];

$jsFiles = [
    "https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
];

// Include CSS files
foreach ($cssFiles as $css_file) {
    echo '<link rel="stylesheet" href="' . $css_file . '">' . PHP_EOL;
}

// Include JavaScript files
foreach ($jsFiles as $js_file) {
    echo '<script src="' . $js_file . '"></script>' . PHP_EOL;
}
?>