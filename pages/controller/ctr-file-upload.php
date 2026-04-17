<?php
include '../../includes/init.php';

if (isset($_FILES['img_input']) || isset($_FILES['profile_input'])) {
    if (isset($_FILES['img_input'])){
        $file = checkFile([
            'file_name'    => 'img_input',
            'allowed_mime' => ["image/jpeg" => "JPG"],
        ]);
    }else if (isset($_FILES['profile_input'])){
        $file = checkFile([
            'file_name'    => 'profile_input',
            'allowed_mime' => ["image/jpeg" => "JPG"],
        ]);
    }

    if ($file['result'] === 'success') {
        $_SESSION['proms-admin']['img_input']['result'] = $file['result'];
        $_SESSION['proms-admin']['img_input']['content'] = $file['content'];
        $_SESSION['proms-admin']['img_input']['type'] = $file['type'];
    } else {
        $_SESSION['proms-admin']['img_input']['result'] = $file['result'];
        $_SESSION['proms-admin']['img_input']['content'] = $file['message'];
    }

    echo "Image Upload Success";
}elseif (isset($_FILES['cert_input'])) {
    $file = checkFile([
        'file_name'    => 'cert_input',
        'allowed_mime' => ["image/jpeg" => "JPG"],
    ]);

    if ($file['result'] === 'success') {
        $_SESSION['proms-admin']['cert_input']['result'] = $file['result'];
        $_SESSION['proms-admin']['cert_input']['content'] = $file['content'];
        $_SESSION['proms-admin']['cert_input']['type'] = $file['type'];
    } else {
        $_SESSION['proms-admin']['cert_input']['result'] = $file['result'];
        $_SESSION['proms-admin']['cert_input']['content'] = $file['message'];
    }

    echo "Certificate Upload Success";
}else{
    http_response_code(400);
    exit('No file received');
}

?>