<?php
include '../includes/init.php';
require '../assets/plugins/brevo/vendor/autoload.php';

$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
$apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(new GuzzleHttp\Client(), $config);

$email = new \SendinBlue\Client\Model\SendSmtpEmail([
    'to' => [['email' => $_SESSION['proms-admin']['reset_email']['email'], 'name' => $_SESSION['proms-admin']['reset_email']['name']]],
    'templateId' => 1,
    'params' => [
        'link_url' => $GLOBALS['INF_CONFIG']['sitehost']. '/reset-pass.php?token=' . $_SESSION['proms-admin']['reset_email']['token'],
    ]
]);

try {
    $result = $apiInstance->sendTransacEmail($email);
    
    unset($_SESSION['proms-admin']['reset_email']);

    Alert::success(array(
        'title' => 'Reset Link Sent!',
        'text'  => 'A password reset link has been sent to your email address. Please check your inbox.',
        'timer' => 3000,
        'path'  => '../login.php'
    ));
} catch (Exception $e) {
    echo 'Exception when sending email: ', $e->getMessage();
}
?>