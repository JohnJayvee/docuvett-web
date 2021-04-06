<?php

namespace App\Libraries\GSuite\Services\Message;

use Carbon\Carbon;
use App\Libraries\GSuite\GoogleConnection;
use App\Libraries\GSuite\Traits\HasDecodableBody;
use App\Libraries\GSuite\Traits\Modifiable;
use App\Libraries\GSuite\Traits\Replyable;
use Google_Service_Gmail;
use Illuminate\Support\Collection;

/**
 * Class SingleMessage
 * @package App\Libraries\GSuite\services
 */
class Mail extends GoogleConnection
{
	use HasDecodableBody,
		Modifiable,
		Replyable {
		Replyable::__construct as private __rConstruct;
		Modifiable::__construct as private __mConstruct;
	}

	/**
	 * @var
	 */
	public $id;

	/**
	 * @var
	 */
	public $internalDate;

	/**
	 * @var
	 */
	public $labels;

	/**
	 * @var
	 */
	public $size;

	/**
	 * @var
	 */
	public $threatId;

	/**
	 * @var \Google_Service_Gmail_MessagePart
	 */
	public $payload;

    /**
     * @var string
     */
    public $snippet;

    /**
     * @var boolean
     */
    public $isUnread;

    /**
     * @var boolean
     */
    public $isStarred;

    /**
     * @var boolean
     */

    public $isImportant;
    /**
     * @var boolean
     */
    public $isDraft;

	/**
	 * @var Google_Service_Gmail
	 */
	public $service;

	/**
	 * SingleMessage constructor.
	 *
	 * @param \Google_Service_Gmail_Message $message
	 * @param bool $preload
	 *
	 */
	public function __construct( \Google_Service_Gmail_Message $message = null, $preload = false )
	{

		$this->service = new Google_Service_Gmail( $this );

		$this->__rConstruct();
		$this->__mConstruct();
		parent::__construct( config() );

		if ( ! is_null( $message ) ) {
			if ( $preload ) {
				$message = $this->service->users_messages->get( $this->userId, $message->getId());
			}

			$this->id = $message->getId();
			$this->internalDate = $message->getInternalDate();
			$this->labels = $message->getLabelIds();
			$this->size = $message->getSizeEstimate();
			$this->threatId = $message->getThreadId();
            $this->payload = $message->getPayload();
            $this->snippet = $message->snippet;
            $this->isUnread = $this->getIsUnread();
            $this->isStarred = $this->getIsStarred();
            $this->isImportant = $this->getIsImportant();
            $this->isDraft = self::getIsDraft($this->labels);
		}
	}

	/**
	 * Returns ID of the email
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Return a UNIX version of the date
	 *
	 * @return int UNIX date
	 */
	public function getInternalDate()
	{
		return $this->internalDate;
	}

	/**
	 * Returns the labels of the email
	 * Example: INBOX, STARRED, UNREAD
	 *
	 * @return array
	 */
	public function getLabels()
	{
		return $this->labels;
	}

	/**
	 * Returns approximate size of the email
	 *
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Returns threat ID of the email
	 *
	 * @return string
	 */
	public function getThreatId()
	{
		return $this->threatId;
	}

	/**
	 * Returns all the headers of the email
	 *
	 * @return Collection
	 */
	public function getHeaders()
	{
		return $this->buildHeaders( $this->payload->getHeaders() );
	}

	/**
	 * Returns the subject of the email
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->getHeader( 'Subject' );
	}

	/**
	 * Returns array of name and email of each recipient
	 *
	 * @return array
	 */
	public function getFrom()
	{
		$from = $this->getHeader( 'From' );

		preg_match( '/<(.*)>/', $from, $matches );

		$name = preg_replace( '/ <(.*)>/', '', $from );
        $name = str_replace(['"', '<', '>'], '', $name);
        $name = preg_replace('/@.+\..+/', '', $name);

		return [
			'name'  => $name,
			'email' => isset( $matches[ 1 ] ) ? $matches[ 1 ] : null,
		];
	}

	/**
	 * Returns email of sender
	 *
	 * @return string|null
	 */
	public function getFromEmail()
	{
		$from = $this->getHeader( 'From' );

		preg_match( '/<(.*)>/', $from, $matches );

		return isset( $matches[ 1 ] ) ? $matches[ 1 ] : null;
	}

	/**
	 * Returns name of the sender
	 *
	 * @return string|null
	 */
	public function getFromName()
	{
		$from = $this->getHeader( 'From' );

		$name = preg_replace( '/ <(.*)>/', '', $from );

		return $name;
	}

	/**
	 * Returns array list of recipients
	 *
	 * @return array
	 */
	public function getTo()
	{
		$allTo = $this->getHeader( 'To' );

		return $this->formatEmailList( $allTo );
	}

	/**
	 * Returns the original date that the email was sent
	 *
	 * @return Carbon
	 */
	public function getDate()
	{
		return Carbon::parse( $this->getHeader( 'Date' ) );
	}

	/**
	 * Returns email of the original recipient
	 *
	 * @return string
	 */
	public function getDeliveredTo()
	{
		return $this->getHeader( 'Delivered-To' );
	}

	/**
	 * @return string
	 */
	public function getPlainTextBody()
	{
        return $this->getBody('text/plain');
	}

	/**
	 * @param $textIfEmpty $raw
	 *
	 * @return string
	 */
	public function getHtmlBody( $textIfEmpty = false )
	{
	    // Try to get HTML
		$content = $this->getBody( 'text/html' );

		// Get Plain Text if no HTML
        $content = $content ?: ($textIfEmpty ? $this->getBody( 'text/plain' ) : '');

        // Add <br> if no HTML tags in content
        if(preg_match( "/\/[a-z]*>/i", $content ) == 0) {
            $content = nl2br($content);
        }
        $content = $this->fixEmails($content);
		return $content ?: false;
	}

	/**
	 * Returns a collection of attachments
	 *
	 * @param bool $preload Preload the attachment's data
	 *
	 * @return Collection
	 * @throws \Exception
	 */
	public function getAttachments( $preload = false )
	{
		$attachments = new Collection( [] );
		$parts = $this->payload->getParts();

		/** @var \Google_Service_Gmail_MessagePart $part */
		foreach ( $parts as $part ) {

			$body = $part->getBody();

			if ( $body->getAttachmentId() ) {
				$attachment = ( new Attachment( $this->getId(), $part ) );
				if ( $preload ) {
					$attachment->loadData();
				}
				$attachments->push(
					$attachment
				);
			}

		}

		return $attachments;

	}

	/**
	 * @return Collection
	 * @throws \Exception
	 */
	public function getAttachmentsWithData()
	{
		return $this->getAttachments( true );
	}

	/**
	 * @return boolean
	 */
	public function hasAttachments()
	{
		$attachments = 0;
		$parts = $this->payload->getParts();

		/**  @var \Google_Service_Gmail_MessagePart $part */
		foreach ( $parts as $part ) {
			$body = $part->getBody();
			if ( $body->getAttachmentId() ) {
				$attachments ++;
				break;
			}
		}

		return ! ! $attachments;
	}

	public function getBody( $type = 'text/plain' )
	{

        $text = $this->getDecodedBody($this->payload->getBody()['data']);

        $body = $text ? $text : $this->findBody($this->payload->getParts(), $type);

        return $body;

	}


    private function findBody($parts, $mimeType)
    {
        $text = false;

        foreach ($parts as $part) {
            if ($part['mimeType'] == $mimeType && $part['body']) {
                $text = $this->getDecodedBody($part['body']->data);

                break;
            }

            if (!$text && $part['parts']) {
                $text = $this->findBody($part['parts'], $mimeType);

                break;
            }
        }

        return $text;
    }

    private function fixEmails($text)
    {
        $regex = '/<(\S+@\S+\.\S+)>/';
        $replace = '<a href="mailto:$1">$1</a>';

        return preg_replace($regex, $replace, $text);
    }

    public function getIsUnread() {
        if (is_array($this->labels)) {
            foreach ($this->labels as $label) {
                if (strtolower($label) == 'unread') {
                    return true;
                }
            }
        }
        return false;
    }

    public function getIsStarred() {
        if (is_array($this->labels)) {
            foreach ($this->labels as $label) {
                if (strtolower($label) == 'starred') {
                    return true;
                }
            }
        }
        return false;
    }

    public function getIsImportant() {
        if (is_array($this->labels)) {
            foreach ($this->labels as $label) {
                if (strtolower($label) == 'important') {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getIsDraft(array $labels) {
        if (is_array($labels)) {
            foreach ($labels as $label) {
                if (strtolower($label) == 'draft') {
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * Get's the gmail information from the Mail
	 *
	 * @return Mail
	 */
	public function load()
	{
		$message = $this->service->users_messages->get( $this->userId, $this->getId() );

		return new self( $message );
	}

	private function buildHeaders( $emailHeaders )
	{
		$headers = [];

		foreach ( $emailHeaders as $header ) {
			/** @var \Google_Service_Gmail_MessagePartHeader $header */

			$head = new \stdClass();

			$head->key = $header->getName();
			$head->value = $header->getValue();

			$headers[] = $head;
		}

		return collect( $headers );

	}

	/**
	 * Returns an array of emails from an string in RFC 822 format
	 *
	 * @param string $emails email list in RFC 822 format
	 *
	 * @return array
	 */
	public function formatEmailList( $emails )
	{
		$all = [];
		$explodedEmails = explode( ',', $emails );

		foreach ( $explodedEmails as $email ) {

			$item = [];

			preg_match( '/<(.*)>/', $email, $matches );

			$item[ 'email' ] = str_replace( ' ', '', isset( $matches[ 1 ] ) ? $matches[ 1 ] : $email );

			$name = preg_replace( '/ <(.*)>/', '', $email );

			if ( starts_with( $name, ' ' ) ) {
				$name = substr( $name, 1 );
			}

			$item[ 'name' ] = str_replace( "\"", '', $name ?: null );

			$all[] = $item;

		}

		return $all;
	}

	/**
	 * Sets the access token in case we wanna use a different token
	 *
	 * @param string $token
	 *
	 * @return Mail
	 */
	public function using( $token )
	{
		$this->setToken($token);

		return $this;
	}

}