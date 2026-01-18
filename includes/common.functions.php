<?php
function safe_redirect($url, $exit = true)
{
    // Only use the header redirection if headers are not already sent
    if (!headers_sent()) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);

        // Optional workaround for an IE bug (thanks Olav)
        header('Connection: close');
    }

    // HTML/JS Fallback:
    // If the header redirection did not work, try to use various methods other methods

    print '<html>';
    print '<head><title>Redirecting you...</title>';
    print '<meta http-equiv="Refresh" content="0;url=' . $url . '" />';
    print '</head>';
    print '<body onload="location.replace(\'' . $url . '\')">';

    // If the javascript and meta redirect did not work,
    // the user can still click this link
    print 'You should be redirected to this URL:<br />';
    print '<a href=' . $url . ">$url</a><br /><br />";

    print 'If you are not, please click on the link above.<br />';

    print '</body>';
    print '</html>';

    // Stop the script here (optional)
    if ($exit)
        exit;
}

function encrypt_data(string $data, string $key = '@b24fcbada320feed38a13d1a6240d795', bool $flag = false): string|false {
    if (!extension_loaded('openssl')) {
        trigger_error('OpenSSL extension is not installed or enabled.', E_USER_WARNING);
        return false;
    }

    if ($data === null || $data === '') {
        trigger_error('Data to encrypt cannot be empty.', E_USER_WARNING);
        return false;
    }

    $cipher = 'AES-128-CBC'; // AES-128 CBC to match original MCRYPT_RIJNDAEL_128
    $key    = substr(hash('sha256', $key, true), 0, 16); // 16-byte key
    $ivLen  = openssl_cipher_iv_length($cipher);
    $iv     = openssl_random_pseudo_bytes($ivLen);

    if ($iv === false) {
        trigger_error('Failed to create initialization vector (IV).', E_USER_WARNING);
        return false;
    }

    $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($encrypted === false) {
        trigger_error('Encryption failed.', E_USER_WARNING);
        return false;
    }

    $output = base64_encode($iv . $encrypted); // prepend IV for decryption
    return $flag ? $output : rawurlencode($output);
}

function decrypt_data(string $encryptedData, string $key = '@b24fcbada320feed38a13d1a6240d795', bool $flag = false): string|false {
    if (!extension_loaded('openssl')) {
        throw new Exception('OpenSSL extension is not available.');
    }

    if (!$flag) {
        $encryptedData = rawurldecode($encryptedData);
    }

    if ($encryptedData === null || $encryptedData === '') {
        throw new Exception('Encrypted data is empty.');
    }

    $cipher = 'AES-128-CBC';
    $key    = substr(hash('sha256', $key, true), 0, 16); // 16-byte key

    $data = base64_decode($encryptedData, true);
    if ($data === false) {
        throw new Exception('Base64 decoding failed.');
    }

    $ivLen = openssl_cipher_iv_length($cipher);
    if (strlen($data) < $ivLen) {
        throw new Exception('Invalid encrypted data: IV is missing or corrupted.');
    }

    $iv            = substr($data, 0, $ivLen);
    $encryptedText = substr($data, $ivLen);

    $decrypted = openssl_decrypt($encryptedText, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($decrypted === false) {
        throw new Exception('Decryption failed.');
    }

    return $decrypted;
}

function status_remark($status)
{
    /*
     * function to display activity status/color.
     *
     * parameter:
     *     $status, int
     *
     * return:
     *     array, [str, str]
     */
    switch ($status) {
        case 0:
            return ['orange', 'Pending'];
            break;
        case 1:
            return ['green', 'Approved'];
            break;
        case 2:
            return ['red', 'Rejected'];
            break;
        default:
            return ['', 'No Status'];
    }
}

/**
 * Log data to the browser console (for debugging).
 * Usage: console('message'); or console(['key' => 'value']);
 */
function console($data, $type = 'log')
{
    if (is_array($data) || is_object($data)) {
        $output = json_encode($data);
    } else {
        $output = addslashes($data);
        $output = '"' . $output . '"';
    }
    echo "<script>console.{$type}({$output});</script>";
}

/**
 * Log data as an error to the browser console.
 */
function console_error($data)
{
    console($data, 'error');
}

/**
 * Log data as a warning to the browser console.
 */
function console_warn($data)
{
    console($data, 'warn');
}

/**
 * Log data as info to the browser console.
 */
function console_info($data)
{
    console($data, 'info');
}

function e($data)
{
    if($data){
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }else{
        $data = '';
    }
    return $data;
}

function setActiveLink($link)
{
    $systemName = basename($GLOBALS['INF_CONFIG']['sitehost']);
    echo "<script>localStorage.setItem('menu_link', '/" . $systemName . '/modules/' . $link . "');</script>";
}

function getDuplicate($dbRows, $post, $m = true)
{
    /**
     * Function to check for duplicate values in a database row against a POST array.
     * @param array $dbRows An array of database rows, where each row is an associative array.
     * @param array $post An array of values from a POST request.
     * @return string A message indicating whether duplicates were found and which values were duplicated.
     */
    $duplicates = array();

    foreach ($dbRows as $record) {
        foreach ($post as $postValue) {
            foreach ($record as $dbValue) {
                if (strcasecmp($dbValue, $postValue) === 0) {
                    $duplicates[] = $postValue;
                }
            }
        }
    }

    $duplicates = array_unique($duplicates);

    // Format each value with bold
    $formatted = array();
    foreach ($duplicates as $val) {
        $formatted[] = "'<strong>" . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "</strong>'";
    }

    $count = count($formatted);

    if ($count === 1) {
        $text = $formatted[0];
    } elseif ($count === 2) {
        $text = $formatted[0] . ' and ' . $formatted[1];
    } else {
        $last = array_pop($formatted);
        $text = implode(', ', $formatted) . ' and ' . $last;
    }

    if ($m) {
        return "Found duplication for $text.";
    } else {
        return $duplicates;
    }
}

function validateRequiredFields(array $requiredFields, $source = null)
{
    $source  = is_array($source) ? $source : $_POST;
    $missing = [];

    foreach ($requiredFields as $field => $label) {
        if (!isset($source[$field]) || trim($source[$field]) === '') {
            $missing[] = $label;
        }
    }

    return $missing;
}

function checkFile(string $file_input_array): string
{
    // If input does not exist
    if (!isset($_FILES[$file_input_array])) {
        return "No file uploaded";
    }

    $file = $_FILES[$file_input_array];

    // If no file name or empty
    if (empty($file['name'])) {
        return "No file uploaded";
    }

    // no file uploaded but field exists
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "{$file['name']} upload error!";
    }

    $tmp  = $file['tmp_name'] ?? '';
    $size = $file['size'] ?? 0;
    $type = $file['type'] ?? '';

    // Size > 10 MB
    if ($size > 10 * 1024 * 1024) {
        return "{$file['name']} must be less than or equal to 10MB";
    }

    // ensure valid file temp
    if (!is_uploaded_file($tmp)) {
        return "{$file['name']} upload error!";
    }

    // accept only PDF
    if ($type !== "application/pdf") {
        return "{$file['name']} must be a PDF file";
    }

    return "Success";
}

?>