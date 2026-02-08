<?php
include '../../includes/init.php';

$validator = new FileValidator(
    maxSizeMB: 10,
    allowedMimeTypes: [
        'image/jpeg',
    ]
);

if (!isset($_FILES['img_input'])) {
    http_response_code(400);
    exit('No file received');
}else{
    $result = $validator->check('img_input');
    
    if($result == 'Success') {
        $file = $_FILES['img_input'];
        $filename = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file['tmp_name']);

        $fileContent = file_get_contents($fileTmpPath);

        $_SESSION['img_input']['content'] = $fileContent;
        $_SESSION['img_input']['status'] = $result;
        $_SESSION['img_input']['type'] = $fileType;
        
        $remarks_array['attachment_file'] = $fileContent;
    }else{
        $_SESSION['img_input']['content'] = $result;
        $_SESSION['img_input']['status'] = "Failed";
    }

    echo $fileContent;
}

?>