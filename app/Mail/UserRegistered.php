<?php

namespace App\Mail;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $user;
    private $password;
    private $selfRegistered;

    public function __construct(User $user, $password, $selfRegistered = true)
    {
        $this->user = $user;
        $this->password = $password;
        $this->selfRegistered = (bool) $selfRegistered;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = $this->selfRegistered ? 'email.user_registered' : 'email.user_created';
        return $this->to($this->user->email, $this->user->name)
            ->subject('Registration in ' . config('app.name'))
            ->view($view, [
                'user' => $this->user,
                'password' => $this->password
            ]);
    }
}
