<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin'          => 'boolean',
    ];

    /**
     * Specify Slack Webhook URL to route notifications to.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        return env('SLACK_WEBHOOK_URL');
    }

    /**
     * Get attendances.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
