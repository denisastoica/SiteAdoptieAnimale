<?php
// Ensure the custom ImageException is available
if (!class_exists('ImageException')) {
    require_once __DIR__ . '/../Exceptions/ImageException.php';
}

/**
 * Helper simplu pentru uploadul imaginilor generale (nu cele din stories).
 */
class Image {
    public static function upload($file) {
        $targetDir = __DIR__ . '/../../public/assets/images/';
        $fileName = time() . '_' . basename($file['name']);
        $targetFile = $targetDir . $fileName;

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed) && move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $fileName;
        }

        // Throw a domain-specific exception so callers can catch image-related errors explicitly
        throw new ImageException("Eroare la încărcarea imaginii.");
    }
}
