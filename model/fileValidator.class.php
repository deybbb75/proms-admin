<?php
/**
 * Class FileValidator
 *
 * A reusable utility class for validating uploaded files.
 *
 * Features:
 * - Validates file upload existence and upload errors
 * - Enforces maximum file size (configured in megabytes)
 * - Validates actual MIME type using finfo (secure, server-side check)
 * - Supports restricting uploads to specific MIME types or allowing all types
 *
 * Usage example:
 * $validator = new FileValidator(20, ['application/pdf', 'image/jpeg']);
 * $result = $validator->check('file_input_name');
 *
 * Returns:
 * - true on successful validation
 * - string error message on failure
 *
 * Notes:
 * - Max file size is provided in MB and converted internally to bytes
 * - MIME type validation is skipped if no allowed types are provided
 *
 * @author Engr. Dave Ellomar Jamilla
 * @version 1.0.0
 * @since PHP 8.0
 * PHP version 8+
 */

class FileValidator
{
    private int $maxSizeBytes;
    private array $allowedMimeTypes;

    public function __construct(
        int $maxSizeMB = 10,           // Size in MB
        array $allowedMimeTypes = []   // Empty = allow any type
    ) {
        $this->maxSizeBytes = $maxSizeMB * 1024 * 1024;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    public function check(string $fileInputName): string|bool
    {
        if (!isset($_FILES[$fileInputName])) {
            return "No file uploaded";
        }

        $file = $_FILES[$fileInputName];

        if (empty($file['name'])) {
            return "No file uploaded";
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "{$file['name']} upload error!";
        }

        $tmp  = $file['tmp_name'] ?? '';
        $size = $file['size'] ?? 0;

        if ($size > $this->maxSizeBytes) {
            $mb = round($this->maxSizeBytes / 1024 / 1024, 2);
            return "{$file['name']} must be less than or equal to {$mb}MB";
        }

        if (!is_uploaded_file($tmp)) {
            return "{$file['name']} upload error!";
        }

        // Detect actual MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $actualMime = finfo_file($finfo, $tmp);
        finfo_close($finfo);

        if (!empty($this->allowedMimeTypes) &&
            !in_array($actualMime, $this->allowedMimeTypes, true)
        ) {
            return "{$file['name']} must be a valid file type";
        }

        return true;
    }
}
?>