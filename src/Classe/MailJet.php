<?php

namespace App\Classe;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MailJet
{
   private $api_key = "96bb715a25f2ec5b0e07aa1a259e5390";
   private $api_key_secret = "f7c9db56b3d9155ae61a4d1972354f05";

   public function send($to_email, $to_name, $subject, $content)
   {
       $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
       $body = [
           'Messages' => [
               [
                   'From' => [
                       'Email' => "aelor666@hotmail.com",
                       'Name' => "MaBoutique - Piquet Aurélien"
                   ],
                   'To' => [
                       [
                           'Email' => $to_email,
                           'Name' => $to_name,
                       ]
                   ],
                   'TemplateID' => 3265446,
                   'TemplateLanguage' => true,
                   'Subject' => $subject,
                   'Variables' => [
                       'content' => $content,
               ]
           ]
       ]];
       $response = $mj->post(Resources::$Email, ['body' => $body]);
       $response->success();
   }
}
