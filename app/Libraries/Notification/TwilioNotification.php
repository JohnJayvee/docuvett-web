<?php namespace App\Libraries\Notification;

use App\Libraries\Notification\ExtNotification;
use Mockery\Exception;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Http\Request;

class TwilioNotification implements SMSNotification
{
    static $twilioClient;

    /**
     * TwilioNotification constructor.
     *
     * @param $sid
     * @param $token
     *
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct($sid, $token)
    {
        self::$twilioClient = new Client($sid, $token);
    }

    /**
     * @param        $TextPart
     * @param        $PhoneNumbers
     * @param array  $mediaUrls
     * @param string $SenderPhoneNumber
     * @param array  $variables
     *
     * @return array
     */
    public static function sendSMS(
        $TextPart,
        $PhoneNumbers,
        $mediaUrls = [],
        $SenderPhoneNumber = '',
        $variables = []
    ) {
        $responses = [];
        foreach ($PhoneNumbers as $Name => $PhoneNumber) {
            try {
//                $PhoneNumbers = $Name => $PhoneNumber
//                $PhoneNumbers = [$Name => [Phone => $PhoneNumber, PersonalVariables => $localVariables]

                if (is_array($PhoneNumber)) {

                    $merged_variables = array_merge($variables,
                        $PhoneNumber['personalVariables']);

                    foreach ($merged_variables as $key => $variable) {
                        $TextPart = str_replace("$$key", $variable, $TextPart);
                    }

                    $responses[]
                        = self::$twilioClient->account->messages->create(
                        $PhoneNumber['phone'],
                        [
                            'from' => $SenderPhoneNumber,
                            // From a valid
                            // Twilio number
                            'body' => $TextPart,
                            'mediaUrl' => $mediaUrls
                            //'statusCallback' => "http://requestb.in/1234abcd"
                        ]
                    );
                } else {
                    $responses[]
                        = self::$twilioClient->account->messages->create(
                        $PhoneNumber,       // Text this number
                        [
                            'from' => $SenderPhoneNumber,
                            // From a valid
                            // Twilio number
//			                    'body' => str_replace('%name%', $Name, $TextPart),
                            'body' => $TextPart,
                            'mediaUrl' => $mediaUrls
                            //'statusCallback' => "http://requestb.in/1234abcd"
                        ]


                    );
                }
            } catch (TwilioException $e) {
//                dd($e->getMessage());
                throw new UnprocessableEntityHttpException($e->getMessage());
            }
        }
        return $responses;
    }

    public function prepareEventData($request)
    {
        return [
            'from' => $request->From,
            'to' => $request->To,
            'message' => $request->Body,
            'provider_class' => null,
            'request_parameters' => json_encode($request->input()),
            'request_body' => $request->getContent()
        ];
    }
}