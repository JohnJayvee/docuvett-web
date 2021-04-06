<?php namespace App\Libraries\Notification;

use Mailjet;
use Mailjet\Resources;


class MailJetNotification implements EmailNotification
{
    static $mjClient;

    public function __construct($key, $secret, $call = true)
    {
        self::$mjClient = new Mailjet\Client($key, $secret, $call, ['version' => 'v3.1']);
    }

    public static function getEmailTemplates($filter = 'user')
    {
        $response = self::$mjClient->get(Resources::$Template, [
            'filters' => [
                'OwnerType' => $filter,
                'Limit'     => 150
            ],
            'Headers' => ['Content-Length' => '0'],
        ]);

        $body = $response->getBody();

        if ($body && isset($body['Data'])) {
            $IDs   = array_column($body['Data'], 'ID');
            $Names = array_column($body['Data'], 'Name');

            return array_combine($IDs, $Names);
        } else {
            return [];
        }
    }

    public static function sendEmail($Subject, $Recipients, $TextPart, $HtmlPart, $FromEmail = '', $FromName = '', $ReplyToEmail = '', $ReplyToName = '', $Attachments = [], $bcc = [])
    {
        $Messages = [];

        //Now we use modern 3.1 format: https://dev.mailjet.com/guides/#send-api-v3-to-v3-1
        foreach (self::convertRecipients($Recipients) as $recipient) {
            $message = [
                'From'     => [
                    'Email' => $FromEmail,
                    'Name'  => $FromName
                ],
                'To'       => [
                    [
                        'Email' => $recipient['Email'],
                        'Name'  => $recipient['Name'],
                    ]
                ],
                'Bcc'      => $bcc,
                'Subject'  => $Subject,
                'TextPart' => $TextPart,
                'HTMLPart' => $HtmlPart,
            ];
            if (!empty($Attachments)) {
                $message['Attachments'] = $Attachments;
            }
            if (!empty($ReplyToEmail) && !empty($ReplyToName)) {
                $message['ReplyTo'] = [
                    'Email' => $ReplyToEmail,
                    'Name'  => $ReplyToName
                ];
            }
            $Messages[] = $message;
        }

        return self::convertResponse(self::$mjClient->post(Resources::$Email, [
            'body' => [
                'Messages' => $Messages
            ]
        ]));
    }

    public static function convertRecipients($Recipients)
    {
        return array_map(
            function ($email, $person) {
                return [
                    'Email' => $email,
                    'Name'  => $person,
                    'Vars'  => [
                        'name'  => trim($person),
                        'email' => $email,
                    ]
                ];
            },
            $Recipients, array_keys($Recipients)
        );
    }

    private static function convertResponse(Mailjet\Response $response)
    {
        return array(
            'status' => $response->getStatus(),
            'data'   => $response->getData()
        );
    }

    public static function inboundEmails($FromEmail, $FromName, $Subject, $Recipients, $TextPart)
    {
    }

    public static function sendEmailTemplate(
        $Subject,
        $Recipients,
        $TemplateID,
        $TemplateArgs = [],
        $FromEmail = '',
        $FromName = '',
        $ReplyToEmail = '',
        $ReplyToName = '',
        $Attachments = [],
        $Module = ''
    ) {
        $body = [
            'FromName'                  => $FromName,
            'FromEmail'                 => $FromEmail,
            'Subject'                   => $Subject,
            'Recipients'                => self::convertRecipients($Recipients), //[['Email' => "passenger1@mailjet.com", 'Name' => "passenger 1"]],
            'MJ-TemplateID'             => $TemplateID,
            'Mj-TemplateLanguage'       => true,
            'MJ-TemplateErrorDeliver'   => 'deliver',
            'MJ-TemplateErrorReporting' => '958190@gmail.com',
            'Vars'                      => $TemplateArgs
        ];

        if (!empty($Attachments)) {
            $email['Attachments'] = $Attachments;
        }

        return self::convertResponse(self::$mjClient->post(Resources::$Email, ['body' => $body]));
    }

    public function prepareEventData($request)
    {
        return [
            'message_id' => $request['MessageID'],
            'module'     => null,
            'params'     => null,
            'recipient'  => $request['email'],
            'status'     => $request['event'],
        ];
    }

}