<?php

namespace Dalab\MailManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Observers\ObjectObserver;

class EmailMessage extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'message_id',
        'subject',
        'text_body',
        'html_body',
        'attachment_count',
        'header',
        'email_id',
        'client_id',
        'folder',
        'is_ready',
        'received_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public static function boot()
    {
        parent::boot();

        self::observe(ObjectObserver::class);
    }
}
