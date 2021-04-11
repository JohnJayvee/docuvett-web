<?php

namespace App\Libraries\GSuite;

use App\Contracts\HasMailbox;
use App\Libraries\GSuite\Exceptions\AuthException;
use App\Libraries\GSuite\Services\Calendar\Calendar;
use App\Libraries\GSuite\Services\Contacts\Contacts;
use App\Libraries\GSuite\Services\Drive\Drive;
use App\Libraries\GSuite\Services\Event\Event;
use App\Libraries\GSuite\Services\Task\Task;
use App\Libraries\GSuite\Services\Label\Label;
use App\Libraries\GSuite\Services\Message\Message;
use App\Libraries\GSuite\Services\Message\Setting;
use App\Models\User\User;
use Google\Auth\Cache\MemoryCacheItemPool;
use Illuminate\Support\Facades\Redirect;

class GSuiteClass extends GoogleConnection
{
    const COMMON_MAILBOX_TYPE = 'All';

    public function __construct($config)
    {
        if (class_basename($config) === 'Application') {
            $config = $config['config'];
        }

        parent::__construct($config);
    }

    /**
     * @param HasMailbox|string|null $mailboxOwner
     * @return Message
     * @throws AuthException
     * @throws \Google_Exception
     */
    public function message($mailboxOwner = null)
    {
        if ($mailboxOwner == self::COMMON_MAILBOX_TYPE) {
            $this->setCommonMailboxOwner();
        } elseif ($mailboxOwner) {
            $this->setMailboxOwner($mailboxOwner);
        }

        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return new Message($this);
    }

    /**
     * @param HasMailbox|string|null $mailboxOwner
     * @return Message
     * @throws AuthException
     * @throws \Google_Exception
     */
    public function unread($mailboxOwner = null)
    {
        if ($mailboxOwner == self::COMMON_MAILBOX_TYPE) {
            $this->setCommonMailboxOwner();
        } elseif ($mailboxOwner) {
            $this->setMailboxOwner($mailboxOwner);
        }

        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return (new Message($this))->unread();
    }

    /**
     * @param HasMailbox|string|null $mailboxOwner
     * @return Setting
     * @throws AuthException
     * @throws \Google_Exception
     */
    public function setting($mailboxOwner = null)
    {
        if ($mailboxOwner == self::COMMON_MAILBOX_TYPE) {
            $this->setCommonMailboxOwner();
        } elseif ($mailboxOwner) {
            $this->setMailboxOwner($mailboxOwner);
        }

        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return new Setting($this);
    }

    /**
     * @param HasMailbox|string|null $mailboxOwner
     * @return Label
     * @throws AuthException
     * @throws \Google_Exception
     */
    public function label($mailboxOwner = null)
    {
        if ($mailboxOwner == self::COMMON_MAILBOX_TYPE) {
            $this->setCommonMailboxOwner();
        } elseif ($mailboxOwner) {
            $this->setMailboxOwner($mailboxOwner);
        }

        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return new Label($this);
    }

    /**
     * @return Calendar
     * @throws AuthException
     */
    public function calendar()
    {
        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return new Calendar($this);
    }

    /**
     * @param null $mailboxOwner
     * @return Event
     * @throws AuthException
     */
    public function event($mailboxOwner = null)
    {
        if ($mailboxOwner == self::COMMON_MAILBOX_TYPE) {
            $this->setCommonMailboxOwner();
        } elseif ($mailboxOwner) {
            $this->setMailboxOwner($mailboxOwner);
        }

        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return new Event($this);
    }


    /**
     * @return Task
     * @throws AuthException
     */
    public function task()
    {
        if ( ! $this->getToken() ) {
            throw new AuthException( 'No credentials found.' );
        }

        return new Task( $this );
    }

    /**
     * @return Drive
     * @throws \Google_Exception
     */
    public function drive()
    {
        $this->setAuthConfig(json_decode(file_get_contents(base_path(config('gsuite.service_account.credentials'))), true));
        $this->setScopes([
            \Google_Service_Drive::DRIVE,
            \Google_Service_Calendar::CALENDAR,
            \Google_Service_Gmail::MAIL_GOOGLE_COM,
        ]);
        $this->setSubject(config('gsuite.service_account.subject'));

        return new Drive($this);
    }

    /**
     * Returns the Gmail user email
     *
     * @return \Google_Service_Gmail_Profile
     */
    public function user()
    {
        return $this->config('email');
    }

    /**
     * @return Contacts
     * @throws AuthException
     */
    public function contacts()
    {
        if (!$this->getToken()) {
            throw new AuthException('No credentials found.');
        }

        return new Contacts($this);
    }

    /**
     * Gets the URL to authorize the user
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->createAuthUrl();
    }

    public function redirect($redirectTo = null)
    {
        if (!empty($redirectTo)) {
            $this->setState(urlencode(json_encode(['redirect' => $redirectTo])));
        }
        return Redirect::to($this->getAuthUrl());
    }

    public function logout()
    {
        $this->revokeToken();
        $this->deleteAccessToken();
    }

    /**
     * Sets a given MailboxOwner to request it's gmail data
     *
     * @param HasMailbox $mailboxOwner
     * @return $this
     */
    public function setMailboxOwner(HasMailbox $mailboxOwner)
    {
        if (empty($mailboxOwner->getEmail())) {
            throw new \InvalidArgumentException(trans('common.user_has_no_email'));
        }

        $this->mailboxOwner = $mailboxOwner;

        $cache = new MemoryCacheItemPool();
        $this->setCache($cache);

        $this->setAccessToken($this->config());
        $http = $this->authorize();
        $this->setHttpClient($http);

        return $this;
    }

    /**
     * Set common Inbox account
     *
     * @return $this
     * @throws \Google_Exception
     */
    public function setCommonMailboxOwner()
    {
        $this->setAuthConfig(json_decode(file_get_contents(base_path(config('gsuite.service_account.credentials'))), true));
        $this->setScopes([
            \Google_Service_Drive::DRIVE,
            \Google_Service_Calendar::CALENDAR,
            \Google_Service_Gmail::MAIL_GOOGLE_COM,
        ]);
        $this->setSubject(config('gsuite.service_account.common_inbox'));

        return $this;
    }

}