<?php namespace App\Libraries\Notification;

use Carbon\Carbon;

interface EmailNotification
{
    public static function getEmailTemplates($filter = 'user');

    public static function sendEmail(
        $Subject,
        $Recipients,
        $TextPart,
        $HtmlPart,
        $FromEmail = '',
        $FromName = '',
        $ReplyToEmail = '',
        $ReplyToName = '',
        $Attachments = []
    );

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
    );

    public static function inboundEmails(
        $FromEmail,
        $FromName,
        $Subject,
        $Recipients,
        $TextPart
    );
}

interface SMSNotification
{
    public static function sendSMS(
        $TextPart,
        $PhoneNumbers,
        $mediaUrls = [],
        $SenderPhoneNumber = '',
        $variables = []
    );
}

class ExtNotification implements EmailNotification, SMSNotification
{
    public static $Email = __CLASS__;
    public static $SMS = __CLASS__;
    public static $storeEmailOnSend;

    /*
     * The flag which determines store or not events in database
     */
    public static $storeSmsOnSend;
    public static $storeEmailModel;

    /*
     * Model that will be used for events storing
     */
    public static $storeSmsModel;
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function getEmailTemplates($filter = 'user')
    {
        return self::$Email->getEmailTemplates($filter);
    }

    /**
     * @param string $Subject
     * @param array $Recipients
     * @param string $TextPart
     * @param string $HtmlPart
     * @param string $FromEmail
     * @param string $FromName
     * @param string $ReplyToEmail
     * @param string $ReplyToName
     * @param array $Attachments
     *
     * @param array $bcc
     *
     * @return mixed
     */
    public static function sendEmail(
        $Subject,
        $Recipients,
        $TextPart,
        $HtmlPart,
        $FromEmail = '',
        $FromName = '',
        $ReplyToEmail = '',
        $ReplyToName = '',
        $Attachments = [],
        $bcc = []
    ) {
        if (empty($FromEmail)) {
            $FromEmail = config('notification.mailer.sender_email');
        }
        if (empty($FromName)) {
            $FromName = config('notification.mailer.sender_name');
        }

        return self::$Email->sendEmail($Subject, $Recipients, $TextPart,
            $HtmlPart, $FromEmail, $FromName, $ReplyToEmail, $ReplyToName,
            $Attachments, $bcc);
    }

    /**
     * @param string $Subject
     * @param array $Recipients
     * @param string $TemplateID
     * @param array $TemplateArgs
     * @param string $FromEmail
     * @param string $FromName
     * @param string $ReplyToEmail
     * @param string $ReplyToName
     * @param array $Attachments
     * @param string $Module
     *
     * @return mixed
     * @throws \ReflectionException
     */
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
        if (empty($FromEmail)) {
            $FromEmail = config('notification.mailer.sender_email');
        }
        if (empty($FromName)) {
            $FromName = config('notification.mailer.sender_name');
        }

        $response = self::$Email->sendEmailTemplate($Subject, $Recipients,
            $TemplateID, $TemplateArgs, $FromEmail, $FromName, $ReplyToEmail,
            $ReplyToName, $Attachments);

        /* Build function parameters array with their names from passed parameters */
        $params = [];
        $ref    = new \ReflectionMethod(self::class, __FUNCTION__);
        foreach ($ref->getParameters() as $param) {
            $name          = $param->name;
            $params[$name] = $$name;
        }
        self::storeNewEvents($response, $params);

        return $response;
    }

    public static function storeNewEvents($response, $params)
    {
        if (self::$storeEmailOnSend) {
            $bulkStatuses = [];
            $current_time = Carbon::now();
            if (isset($response['data'])) {
                if (isset($response['data']['Sent'])
                    && is_array($response['data']['Sent'])
                ) {
                    foreach ($response['data']['Sent'] as $sentEmail) {
                        $userEmail = $sentEmail['Email'];

                        $paramsForSingleUser = $params;
                        $originalUserKey     = array_search($userEmail,
                            $params['Recipients']);
                        if (empty($originalUserKey)) {
                            $originalUserKey = '';
                        }
                        $paramsForSingleUser['Recipients']
                            = [$originalUserKey => $userEmail];

                        $bulkStatuses[] = [
                            'message_id' => $sentEmail['MessageID'],
                            'module'     => $paramsForSingleUser['Module'],
                            'recipient'  => $userEmail,
                            'status'     => 'queued',
                            'params'     => json_encode($paramsForSingleUser),
                            'created_at' => $current_time,
                            'updated_at' => $current_time,
                        ];
                    }
                }
            }
            (new self::$storeEmailModel)->insert($bulkStatuses);
        }
    }

    /**
     * @param $message_id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \ReflectionException
     */
    public static function reSendEmailTemplate($message_id)
    {

        try {
            $params = (new self::$storeEmailModel)
                ->where('message_id', $message_id)
                ->orderBy('created_at', 'desc')
                ->firstOrFail()
                ->params;
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 200);
        }

        /* Rebuild actual function parameters and their orders, according actual state */
        $rebuildParams = [];
        $ref           = new \ReflectionMethod(self::class, 'sendEmailTemplate');
        foreach ($ref->getParameters() as $param) {
            $name                 = $param->name;
            $rebuildParams[$name] = (isset($params[$name])) ? $params[$name]
                : null;
        }

        return call_user_func_array(__CLASS__ . '::sendEmailTemplate',
            $rebuildParams);
    }

    public static function inboundEmails(
        $FromEmail,
        $FromName,
        $Subject,
        $Recipients,
        $TextPart
    ) {
        return self::$Email->inboundEmails($FromEmail, $FromName, $Subject,
            $Recipients, $TextPart);
    }

    public static function sendSMS(
        $TextPart,
        $PhoneNumbers,
        $mediaUrls = [],
        $SenderPhoneNumber = '',
        $variables = []
    ) {
        if (empty($SenderPhoneNumber)) {
            $SenderPhoneNumber = config('notification.sms.sender_number');
        }
        $response = self::$SMS->sendSMS($TextPart, $PhoneNumbers, $mediaUrls,
            $SenderPhoneNumber, $variables);

//        self::storeNewSmsEvents($response);
        return $response;
    }

    public static function storeNewSmsEvents($responce)
    {
        if (self::$storeSmsOnSend) {
            $eventsData = self::$SMS->prepareEventData($responce);
            (new self::$storeSmsModel)->insert($eventsData);
        }
    }

    public static function handleEvent($request)
    {
        $eventData = self::$Email->prepareEventData($request);
        try {
            $eventInDB = (new self::$storeEmailModel)->where('message_id',
                $request['MessageID'])->orderBy('created_at',
                'desc')->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 200);
        }
        if (!$eventData['module']) {
            $eventData['module'] = $eventInDB->module;
        }
        if (!$eventData['params']) {
            $eventData['params'] = $eventInDB->params;
        }

        $listener_handler = config('notification.mailer.listener_handler', '');
        if (!empty($listener_handler)) {
            (new $listener_handler())->handle();
        } else {
            return self::storeEvent($eventData);
        }
    }

    public static function storeEvent($data)
    {
        return (new self::$storeEmailModel)->create($data);
    }

    public static function handleIncomingSmsEvent(
        $request,
        $nativeResponse = false
    ) {
        $eventData = self::$SMS->prepareEventData($request);
        if (!$eventData['provider_class']) {
            $eventData['provider_class'] = 'TwilioProvider';
        }
        try {
            $listener_handler = config('notification.sms.listener_handler', '');
            if (!empty($listener_handler)) {
                (new $listener_handler())->handle();
            } else {
                $inserted = (new self::$storeSmsModel)->create($eventData);
                $result   = [
                    'Status' => 'success',
                    'Data'   => $inserted->toArray()
                ];

                return $nativeResponse ? $result
                    : response()->json($result, 200);
            }

        } catch (\Exception $e) {
            return $nativeResponse ? false
                : response()->json(['Error' => $e->getMessage()], 200);
        }
    }

    public static function handleIncomingEmailEvent(
        $request,
        $nativeResponse = false
    ) {
        $eventData = self::$Email->prepareEventData($request);
        if (!$eventData['provider_class']) {
            $eventData['provider_class'] = 'GmailProvider';
        }
        try {
            $listener_handler = config('notification.email.listener_handler',
                '');
            if (!empty($listener_handler)) {
                (new $listener_handler())->handle();
            } else {
                $inserted = (new self::$storeSmsModel)->create($eventData);
                $result   = [
                    'Status' => 'success',
                    'Data'   => $inserted->toArray()
                ];

                return $nativeResponse ? $result
                    : response()->json($result, 200);
            }

        } catch (\Exception $e) {
            return $nativeResponse ? false
                : response()->json(['Error' => $e->getMessage()], 200);
        }
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

}

$SMSClass  = config('notification.sms.class');
$MailClass = config('notification.mailer.class');

ExtNotification::$Email = new $MailClass(
    config('notification.mailer.api_key'),
    config('notification.mailer.api_secret')
);

ExtNotification::$storeEmailModel  = config('notification.mailer.store_model');
ExtNotification::$storeEmailOnSend = config('notification.mailer.store_on_send', false);

ExtNotification::$SMS = new $SMSClass(
    config('notification.sms.sid'),
    config('notification.sms.token')
);

ExtNotification::$storeSmsModel  = config('notification.sms.store_model');
ExtNotification::$storeSmsOnSend = config('notification.sms.store_on_send', false);