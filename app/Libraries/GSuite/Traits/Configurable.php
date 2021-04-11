<?php

namespace App\Libraries\GSuite\Traits;

use App\Contracts\HasMailbox;
use App\Libraries\Utils\Utils;
use App\Models\User\User;
use Google_Service_Calendar;
use Google_Service_Drive;
use Google_Service_Gmail;
use Google_Service_Tasks;
use Google_Service_PeopleService;
use Illuminate\Support\Arr;


/**
 * Trait Configurable
 * @package Dacastro4\LaravelGmail\Traits
 */
trait Configurable
{

	private $_config;

	public function __construct( $config )
	{
		$this->_config = $config;
	}

	public function config( $string = null )
	{
		$fileName = $this->mailboxOwner ?
            $this->mailboxOwner->getGSuiteTokenFileName() :
            $this->getFileName();

		$file = storage_path( "app/gsuite/tokens/{$fileName}.json" );

		if ( file_exists( $file ) ) {
			$config = json_decode(
				file_get_contents( $file ),
				true
			);

			if ( $string ) {
				if ( isset( $config[ $string ] ) ) {
					return $config[ $string ];
				}
			} else {
				return $config;
			}

		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getConfigs()
	{
		return [
			'client_secret' => $this->_config[ 'gsuite.client_secret' ],
			'client_id'     => $this->_config[ 'gsuite.client_id' ],
			'redirect_uri'  => url( $this->_config[ 'gsuite.redirect_url' ] ),
		];
	}

	private function getUserScopes()
	{
		return array_merge(
			[
				Google_Service_Gmail::GMAIL_READONLY,
			], $this->mapScopes() );
	}

	private function configApi()
	{
		$type = $this->_config[ 'gsuite.access_type' ];
		$approval_prompt = $this->_config[ 'gsuite.approval_prompt' ];

		$this->setScopes( $this->getUserScopes() );

		$this->setAccessType( $type );

		$this->setApprovalPrompt( $approval_prompt );
	}

	private function getFileName(HasMailbox $mailboxOwner = null)
	{
	    if ($mailboxOwner) {
            return $mailboxOwner->getGSuiteTokenFileName();
        }

	    if ($user = Utils::getCurrentUser()) {
	        return $user->getGSuiteTokenFileName();
        } elseif (!empty($this->emailAddress)) {
	        if($user = User::where('email', 'like', $this->emailAddress)->first()) {
                return $user->getGSuiteTokenFileName();
            }
        }
        return null;
	}

	private function mapScopes()
	{
		$scopes = $this->_config[ 'gsuite.scopes' ];
		$mappedScopes = [];

		if ( ! empty( $scopes ) ) {
			foreach ( $scopes as $scope ) {
				$mappedScopes[] = $this->scopeMap( $scope );
			}
		}

		return $mappedScopes;
	}

	private function scopeMap( $scope )
	{
		$scopes = [
            'gmail'     => Google_Service_Gmail::MAIL_GOOGLE_COM,
            'calendar'  => Google_Service_Calendar::CALENDAR,
            'gdrive'    => Google_Service_Drive::DRIVE,
            'contacts'  => Google_Service_PeopleService::CONTACTS,
            'tasks'    => Google_Service_Tasks::TASKS,
		];

		return Arr::get( $scopes, $scope );
	}

	public abstract function setScopes( $scopes );

	public abstract function setAccessType( $type );

	public abstract function setApprovalPrompt( $approval );

}