<?php
include '../includes/init.php';

$validator = new FileValidator(
    maxSizeMB: 10,
    allowedMimeTypes: [
        'image/jpeg',
    ]
);

if (!isset($_FILES['prog_image'])) {
    http_response_code(400);
    exit('No file received');
}else{
    $result = $validator->check('prog_image');
    
    if($result == 'Success') {
        $file = $_FILES['prog_image'];
        $filename = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file['tmp_name']);

        $fileContent = file_get_contents($fileTmpPath);

        $_SESSION['prog_image']['content'] = $fileContent;
        $_SESSION['prog_image']['status'] = $result;
        $_SESSION['prog_image']['type'] = $fileType;
        
        $remarks_array['attachment_file'] = $fileContent;
    }else{
        $_SESSION['prog_image']['content'] = $result;
        $_SESSION['prog_image']['status'] = "Failed";
    }

    echo $fileContent;
}

?>