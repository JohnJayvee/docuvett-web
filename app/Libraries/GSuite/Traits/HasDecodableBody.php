<?php

namespace App\Libraries\GSuite\Traits;

trait HasDecodableBody
{

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function getDecodedBody( $content )
	{
        $content = base64_decode(strtr($content, '-_', '+/'));
        return !empty($content) ? $content : false;
	}

    public function getBody(\Google_Service_Gmail_MessagePart $payload, $type = 'text/plain' )
    {

        $text = $this->getDecodedBody($payload->getBody()['data']);

        $body = $text ?: $this->findBody($payload->getParts(), $type);

        return $body;

    }


    public function findBody($parts, $mimeType)
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

}