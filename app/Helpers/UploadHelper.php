<?php
/**
 * Helpers for uploaded files
 */
function upload_url(?string $filename): string {
  $filename = trim((string)$filename);
  if ($filename === '') {
    return '/site/public/assets/images/default-pet.jpg';
  }

  // Normalize filename to avoid directory traversal
  $filename = basename($filename);
  $filePath = __DIR__ . '/../../public/uploads/' . $filename;
  if (is_file($filePath)) {
    return '/site/public/uploads/' . $filename;
  }

  return '/site/public/assets/images/default-pet.jpg';
}
