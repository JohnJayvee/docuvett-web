<?php

namespace App\Libraries\GSuite\Services\Message;

use App\Libraries\GSuite\GoogleConnection;
use App\Libraries\GSuite\Traits\HasDecodableBody;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;

class Attachment extends GoogleConnection
{
	use HasDecodableBody;

	/**
	 * @var
	 */
	public $body;
	/**
	 * @var
	 */
	public $id;
	/**
	 * @var
	 */
	public $filename;
	/**
	 * @var
	 */
	public $mimeType;
	/**
	 * @var
	 */
	public $size;
    /**
     * @var
     */
    public $data;
	/**
	 * @var Google_Service_Gmail
	 */
	private $service;

	/**
	 * @var
	 */
	private $messageId;

	/**
	 * Attachment constructor.
	 *
	 * @param $singleMessageId
	 * @param \Google_Service_Gmail_MessagePart $part
	 */
	public function __construct( $singleMessageId, \Google_Service_Gmail_MessagePart $part )
	{
		parent::__construct(config());

		$this->service = new Google_Service_Gmail( $this );

		$body = $part->getBody();
		$this->id = $body->getAttachmentId();
		$this->size = $body->getAttachmentId();
		$this->filename = $part->getFilename();
		$this->mimeType = $part->getMimeType();
		$this->messageId = $singleMessageId;
		$this->data = null;
	}

	/**
	 * Retuns attachment ID
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns attachment file name
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->filename;
	}

	/**
	 * Returns mime type of the attachment
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->getMimeType();
	}

	/**
	 * Returns approximate size of the attachment
	 *
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @throws \Exception
	 */
	public function getData()
	{
		$attachment = $this->service->users_messages_attachments->get( $this->userId, $this->messageId, $this->id );

		return $attachment->getData();
	}

    /**
     * @throws \Exception
     */
    public function loadData()
    {
        $attachment = $this->service->users_messages_attachments->get( $this->userId, $this->messageId, $this->id );

        $this->data = strtr($attachment->getData(), '-_', '+/');

        return $this;
    }

	/**
	 * @param string $path
	 * @param string|null $filename
	 *
	 * @param string $disk
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function saveAttachmentTo( $path = null, $filename = null, $disk = 'local' )
	{

		$data = $this->getDecodedBody( $this->getData() );

		if ( ! $data ) {
			throw new \Exception( 'Could not get the attachment.' );
		}

		$filename = $filename ?: $this->filename;

		if ( is_null($path) ) {
			$path = '/';
		} else {
			if ( ! ends_with( '/', $path ) ) {
				$path = "{$path}/";
			}
		}

		$filePathAndName = "{$path}{$filename}";

		Storage::disk($disk)->put( $filePathAndName, $data );

		return $filePathAndName;

	}
}