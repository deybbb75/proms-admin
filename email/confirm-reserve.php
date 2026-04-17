<?php
include '../includes/init.php';
require '../assets/plugins/brevo/vendor/autoload.php';

$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
$apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(new GuzzleHttp\Client(), $config);

$email = new \SendinBlue\Client\Model\SendSmtpEmail([
    'to' => [['email' => $_SESSION['proms']['confirm_email']['email'], 'name' => $_SESSION['proms']['confirm_email']['name']]],
    'templateId' => 6,
    'params' => [
        'name' => $_SESSION['proms']['confirm_email']['name'],
        'program' => $_SESSION['proms']['confirm_email']['program'],
    ]
]);

try {
    $result = $apiInstance->sendTransacEmail($email);

    Alert::success(array(
        'title' => 'Update Successful',
        'html'  => 'Reservation successfully updated.',
        'path'  => '../pages/pending.php'
    ));
    
} catch (Exception $e) {
    echo 'Exception when sending email: ', $e->getMessage();
}
?>