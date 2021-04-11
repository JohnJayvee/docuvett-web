<?php

namespace App\Libraries\GSuite\Services\Message;

use App\Helpers\FormatterHelper;
use App\Libraries\GSuite\GSuiteClass;
use App\Libraries\GSuite\Traits\HasDecodableBody;
use App\Libraries\GSuite\Traits\SendsParameters;
use App\Libraries\Utils\Utils;
use App\Models\User\User;
use App\Services\Formatter\Formatter;
use App\Services\Formatter\Formatters\GmailFormatters\GmailTimeFormatter;
use Google_Service_Exception;
use Google_Service_Gmail;
use Illuminate\Support\Collection;

class Message
{

    use SendsParameters;
    use HasDecodableBody;

    public $client;

    public $service;

    public $preload = false;

    public $withTotal = false;

    /**
     * Optional parameter for getting single and multiple emails
     *
     * @var array
     */
    protected $params = [];

    const FIELD_UPDATED_AT = 'updated_at';
    const FIELD_INTERNAL_DATE = 'internalDate';

    /**
     * Message constructor.
     *
     * @param GSuiteClass $client
     */
    public function __construct( GSuiteClass $client )
    {
        $this->client = $client;
        $this->service = new Google_Service_Gmail( $client );
    }

    /**
     * Returns a collection of Mail instances
     *
     * @param null|string $pageToken
     *
     * @return array
     */
    public function all( $pageToken = null )
    {
        if ( ! is_null( $pageToken ) ) {
            $this->add( $pageToken, 'pageToken' );
        }
        $messagesResponse = $this->service->users_messages->listUsersMessages( $this->client->userId, $this->params );

        $this->client->setUseBatch(true);
        $batch = new \Google_Http_Batch($this->client, false, null, 'batch/gmail/v1');

        $messages = $messagesResponse->getMessages();
        if (empty($messages)) {
            return [
                'messages' => [],
                'pageToken' => $messagesResponse->getNextPageToken(),
                'sizeEstimate' => 0
            ];
        }
        foreach ( $messages as $message ) {
            $request = $this->service->users_messages->get($this->client->userId, $message->getId(), ['format' => 'metadata']);
            $batch->add($request, $message->getId());
        }
        $batchResponse = $batch->execute();

        if ($this->withTotal) {
            $total = $this->total();
        } else {
            $total = $messagesResponse->getResultSizeEstimate();
        }

        $this->client->setUseBatch(false);

        return [
            'messages' => $this->prepareListResponse($batchResponse),
            'pageToken' => $messagesResponse->getNextPageToken(),
            'sizeEstimate' => $total
        ];
    }

    /**
     * @param array $response
     * @return array $result
     */
    private function prepareListResponse(array $response): array
    {
        $messages = [];
        foreach ($response as $key => $message) {
            $messages[] = $this->prepareSingleResponse($message);
        }
        $formatter = new Formatter();
        $gmailTimeFormatter = new GmailTimeFormatter($formatter);
        $result = (new FormatterHelper())->prepare(
            $gmailTimeFormatter,
            $messages
        );

        return $result;
    }

    private function prepareSingleResponse ($message)
    {
        $payload = $message->getPayload();

        $data = [
            'id' => $message->getId(),
            'historyId' => $message->getHistoryId(),
            'internalDate' => $message->getInternalDate(),
            'labelIds' => $message->getLabelIds(),
            'sizeEstimate' => $message->getSizeEstimate(),
            'snippet' => $message->getSnippet(),
            'threadId' => $message->getThreadId(),
            'isDraft' => Mail::getIsDraft($message->labelIds),
            'isUnread' => false,
            'isStarred' => false,
            'isImportant' => false,
            'attachments' => [],
            'bodyHTML' => '',
            'bodyText' => '',
            'from' => $this->parseEmailHeader('From', $payload['headers']),
            'to' => $this->parseEmailHeader('To', $payload['headers']),
            'sender' => $this->parseEmailHeader('Sender', $payload['headers']),
            'opened_at' => null,
        ];

        if (is_array($data['labelIds'])) {
            foreach ($data['labelIds'] as $label) {
                if (strtolower($label) == 'unread') {
                    $data['isUnread'] =  true;
                }
                if (strtolower($label) == 'starred') {
                    $data['isStarred'] =  true;
                }
                if (strtolower($label) == 'important') {
                    $data['isImportant'] =  true;
                }
            }
        }
        $data['subject'] = $this->getHeader('Subject', $payload['headers']);
        $data['humanDate'] = Utils::getHumanDateTime($this->getHeader('Date', $payload['headers']));
        $data[self::FIELD_UPDATED_AT] = $this->getHeader('Date', $payload['headers']);

        if (!empty($payload['parts']) || !empty($payload['body'])) {

            $data['bodyHTML'] = $this->getHtmlBody($payload, true);
            $data['bodyText'] = $this->getPlainTextBody($payload);
            $data['attachments'] = $this->getAttachments($data['id'], $payload);
        }
        return $data;
    }

    private function parseEmailHeader($headerName, $headers)
    {
        $value = [
            'name' => '',
            'email' => ''
        ];
        $header = $this->getHeader($headerName, $headers);
        if ($header) {
            preg_match( '/<(.*)>/', $header, $matches );
            $name = preg_replace( '/ <(.*)>/', '', $header );
            $name = str_replace(['"', '<', '>'], '', $name);
            $name = preg_replace('/@.+\..+/', '', $name);
            $value['name'] = $name;
            $value['email'] = isset( $matches[ 1 ] ) ? $matches[ 1 ] : str_replace(['"', '<', '>'], '', $header);
        }
        return $value;

    }

    private function getHeader($headerName, $headers)
    {
        $value = null;

        foreach ( $headers as $header ) {
            if ( $header['name'] === $headerName ) {
                $value = $header['value'];
                break;
            }
        }

        if ( is_array( $value ) ) {
            return isset( $value[ 1 ] ) ? $value[ 1 ] : null;
        }

        return $value;
    }

    public function total() {
        $params = $this->params;
        unset($params['pageToken']);
        unset($params['maxResults']);
        $this->client->setUseBatch(false);
        $params['maxResults'] = 500;
        $response = $this->service->users_messages->listUsersMessages( $this->client->userId, $params );
        return $response->getResultSizeEstimate();
    }
    /**
     * Limit the messages coming from the query
     *
     * @param int $number
     *
     * @return Message
     */
    public function take( $number )
    {
        $this->params[ 'maxResults' ] = abs( (int) $number );

        return $this;
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function get(string $id )
    {
        $message = $this->service->users_messages->get( $this->client->userId, $id );

        return $this->prepareSingleResponse($message);
    }


    /**
     * @param array $ids
     *
     * @return array
     */
    public function getMultiple(array $ids )
    {
        $this->client->setUseBatch(true);
        $batch = new \Google_Http_Batch($this->client, false, null, 'batch/gmail/v1');

        foreach ( $ids as $id ) {
            $request = $this->service->users_messages->get($this->client->userId, $id, ['format' => 'metadata']);
            $batch->add($request, $id);
        }
        $batchResponse = $batch->execute();

        return $this->prepareListResponse($batchResponse);
    }

    /**
     * Filter to get only unread emails
     *
     * @return $this
     */
    public function unread()
    {
        $this->add( 'is:unread' );

        return $this;
    }

    /**
     * Filter by subject
     *
     * @param $query
     *
     * @return $this
     */
    public function subject( $query )
    {
        $this->add( "[{$query}]" );

        return $this;
    }

    /**
     * Filter to get only emails from a specific email address
     *
     * @param $email
     *
     * @return $this
     */
    public function from(string $email)
    {
        if ($email) {
            $this->add("from:{$email}");
        }

        return $this;
    }

    /**
     * Filter to get only emails to a specific email address
     *
     * @param $email
     *
     * @return $this
     */
    public function to(string $email)
    {
        if ($email) {
            $this->add("to:{$email}");
        }

        return $this;
    }


    public function search( $search )
    {
        $this->add( "{$search}" );

        return $this;
    }

    /**
     * Filters emails by tag
     * Example:
     * * starred
     * * inbox
     * * spam
     * * chats
     * * sent
     * * draft
     * * trash
     *
     * @param $box
     *
     * @return Message
     */
    public function in( $box = 'inbox' )
    {
        $this->add( "in:{$box}" );

        return $this;
    }

    /**
     * Search for messages sent during a certain time period
     * Example: 2004/04/16
     *
     * @param string $afterDate
     * @return Message
     */
    public function after(string $afterDate)
    {
        if ($afterDate) {
            $this->add('after:' . $afterDate);
        }

        return $this;
    }

    /**
     * Search for messages sent during a certain time period
     * Example: 2004/04/16
     *
     * @param string $beforeDate
     * @return Message
     */
    public function before(string $beforeDate)
    {
        if ($beforeDate) {
            $this->add('before:' . $beforeDate);
        }

        return $this;
    }


    /**
     * Filters emails by labels
     *
     * @param array $labels
     *
     * @return Message
     */
    public function labels( array $labels = [])
    {
        $this->setLabels( $labels );

        return $this;
    }

    /**
     * Determines if the email has attachments
     *
     * @return $this
     */
    public function hasAttachment()
    {
        $this->add( 'has:attachment' );

        return $this;
    }

    /**
     * Preload the information on each Mail objects.
     * If is not preload you will have to call the load method from the Mail class
     * @see Mail::load()
     *
     * @return $this
     */
    public function preload()
    {
        $this->preload = true;

        return $this;
    }
    public function withTotal(bool $value)
    {
        $this->withTotal = $value;

        return $this;
    }

    public function getPlainTextBody(\Google_Service_Gmail_MessagePart $payload)
    {
        return $this->getBody($payload, 'text/plain');
    }

    /**
     * @param $payload
     * @param $textIfEmpty
     *
     * @return string
     */
    public function getHtmlBody(\Google_Service_Gmail_MessagePart $payload, $textIfEmpty = false )
    {
        // Try to get HTML
        $content = $this->getBody($payload, 'text/html');

        // Get Plain Text if no HTML
        $content = $content ?: ($textIfEmpty ? $this->getBody($payload, 'text/plain') : '');

        // Add <br> if no HTML tags in content
        if(preg_match( "/\/[a-z]*>/i", $content ) == 0) {
            $content = nl2br($content);
        }
        $content = $this->fixEmails($content);
        return $content ?: '';
    }

    /**
     * Returns a collection of attachments
     *
     * @param $messageId
     * @param $payload
     *
     * @return Collection
     * @throws \Exception
     */
    public function getAttachments(string $messageId, \Google_Service_Gmail_MessagePart $payload)
    {
        $attachments = new Collection( [] );
        $parts = $payload->getParts();

        /** @var \Google_Service_Gmail_MessagePart $part */
        foreach ( $parts as $part ) {

            $body = $part->getBody();

            if ( $body->getAttachmentId() ) {
                $attachment = ( new Attachment( $messageId, $part ) );
                $attachments->push(
                    $attachment
                );
            }

        }

        return $attachments;

    }

    public function getAttachment(string $messageId, string $attachmentId)
    {
        $attachment = $this->service->users_messages_attachments->get( $this->client->userId, $messageId, $attachmentId );
        return $attachment;

    }

    private function fixEmails($text)
    {
        $regex = '/<(\S+@\S+\.\S+)>/';
        $replace = '<a href="mailto:$1">$1</a>';

        return preg_replace($regex, $replace, $text);
    }

    public function batchDelete(array $ids)
    {
        $post = new \Google_Service_Gmail_BatchDeleteMessagesRequest();
        $post->setIds($ids);
        return $this->service->users_messages->batchDelete($this->client->userId, $post);
    }

    public function batchModify(array $ids, array $data)
    {
        $post = new \Google_Service_Gmail_BatchModifyMessagesRequest();
        $post->setIds($ids);
        if (!empty($data['addLabelIds'])) {
            $post->setAddLabelIds($data['addLabelIds']);
        }
        if (!empty($data['removeLabelIds'])) {
            $post->setRemoveLabelIds($data['removeLabelIds']);
        }
        return $this->service->users_messages->batchModify($this->client->userId, $post);
    }

    public function draft(array $data)
    {
        $email_content = (new \Swift_Message())
            ->setSubject($data['subject'])
            ->setFrom($this->client->user())
            ->setTo($data['to']['email'], $data['to']['name'])
            ->setBody($data['bodyHTML']);
        $email = new \Google_Service_Gmail_Message();
        $email->setRaw(
            strtr(
                base64_encode($email_content->toString()),
                ['+' => '-', '/' => '_']
            )
        );
        $email->setLabelIds(['DRAFT']);
        $email->setThreadId($data['threadId']);

        return $this->service->users_messages->insert($this->client->userId, $email);
    }

    public function send(array $data)
    {
        $email_content = (new \Swift_Message())
            ->setSubject($data['subject'])
            ->setFrom($this->client->user())
            ->setTo($data['to']['email'], $data['to']['name'])
            ->setBody($data['bodyHTML'], 'text/html');
        $email = new \Google_Service_Gmail_Message();
        $email->setRaw(
            strtr(
                base64_encode($email_content->toString()),
                ['+' => '-', '/' => '_']
            )
        );
        try {
            $email->setThreadId($data['threadId']);
            return $this->service->users_messages->send($this->client->userId, $email);
        } catch (Google_Service_Exception $e) {
            $email->setThreadId('');
            return $this->service->users_messages->send($this->client->userId, $email);
        }
    }

    /**
     * Send a message from given users account if GSuite token exists
     *
     * @param MessageDTO $messageDTO
     * @param User $from
     * @return bool
     */
    public function sendFrom(MessageDTO $messageDTO, User $from)
    {
        $client = clone $this->client;
        $client->setMailboxOwner($from);
        $service = new Google_Service_Gmail($client);

        // check if given user has auth token
        if (!$client->config()) {return false;}

        $email_content = (new \Swift_Message())
            ->setSubject($messageDTO->getSubject())
            ->setFrom($client->user())
            ->setTo($messageDTO->getToEmail(), $messageDTO->getToName())
            ->setBcc($messageDTO->getBcc())
            ->setCc($messageDTO->getCc())
            ->setBody($messageDTO->getBody(), $messageDTO->getContentType());

        $email = new \Google_Service_Gmail_Message();
        $email->setRaw(
            strtr(
                base64_encode($email_content->toString()),
                ['+' => '-', '/' => '_']
            )
        );

        $email->setThreadId($messageDTO->getThreadId());
        $sentMessage = $service->users_messages->send($client->userId, $email);

        // true if message sent
        return $sentMessage instanceof \Google_Service_Gmail_Message;
    }

    public function update($messageId, array $data)
    {
        $post = new \Google_Service_Gmail_ModifyMessageRequest();
        if (!empty($data['addLabelIds'])) {
            $post->setAddLabelIds(is_array($data['addLabelIds']) ? $data['addLabelIds'] : [$data['addLabelIds']]);
        }
        if (!empty($data['removeLabelIds'])) {
            $post->setRemoveLabelIds(is_array($data['removeLabelIds']) ? $data['removeLabelIds'] : [$data['removeLabelIds']]);
        }
        $this->service->users_messages->modify($this->client->userId, $messageId, $post);

        return $this->get($messageId);
    }

    public function destroy($messageId)
    {
        return $this->service->users_messages->delete($this->client->userId, $messageId);
    }
}