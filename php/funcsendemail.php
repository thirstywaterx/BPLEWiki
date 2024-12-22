<?php
require_once('...../vendor/autoload.php');
function sendemail($subject,$sendcontent,$toemail,$from=""){
    
    $config = ElasticEmail\Configuration::getDefaultConfiguration()
            ->setApiKey('X-ElasticEmail-ApiKey', '');
     
    $apiInstance = new ElasticEmail\Api\EmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    
    $email = new \ElasticEmail\Model\EmailMessageData(array(
        "recipients" => array(
            new \ElasticEmail\Model\EmailRecipient(array("email" => $toemail))
        ),
        "content" => new \ElasticEmail\Model\EmailContent(array(
            "body" => array(
                new \ElasticEmail\Model\BodyPart(array(
                    "content_type" => "HTML",
                    "content" => $sendcontent
                ))
            ),
            "from" => $from,
            "subject" => $subject
        ))
    ));
     
    try {
        $apiInstance->emailsPost($email);
        return array("success"=>true);
    } catch (Exception $e) {
        return array("success"=>false,"notice"=>'Exception when calling EE API: '. $e->getMessage());
    }

}
?>