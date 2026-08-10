<?php
declare(strict_types=1);

function upload_item_image(array $file, array $config): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new RuntimeException('The image upload failed.');
    if (($file['size'] ?? 0) > (int)$config['upload_max_bytes']) throw new RuntimeException('The image is larger than the allowed limit.');

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $config['allowed_image_types'], true)) throw new RuntimeException('Only JPG, PNG and WEBP images are allowed.');

    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $filename = bin2hex(random_bytes(18)) . '.' . $extensions[$mime];
    $directory = ROOT_PATH . '/uploads/items';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) throw new RuntimeException('Could not create the upload directory.');
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) throw new RuntimeException('Could not save the uploaded image.');
    return 'uploads/items/' . $filename;
}
