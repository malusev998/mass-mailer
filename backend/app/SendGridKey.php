<?php

namespace App;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SendGridKey
 *
 * @package App
 * @property-read \App\Application $application
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Notify[] $messages
 * @property-read int|null $messages_count
 * @method static Builder|SendGridKey newModelQuery()
 * @method static Builder|SendGridKey newQuery()
 * @method static Builder|SendGridKey query()
 * @method static Builder|SendGridKey whereApplicationId($value)
 * @method static Builder|SendGridKey whereCreatedAt($value)
 * @method static Builder|SendGridKey whereId($value)
 * @method static Builder|SendGridKey whereKey($value)
 * @method static Builder|SendGridKey whereNumberOfMessages($value)
 * @method static Builder|SendGridKey whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $id
 * @property string $key
 * @property int $number_of_messages
 * @property int $application_id
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
class SendGridKey extends Model
{
    protected $fillable = [
        'key',
        'number_of_messages',
        'application_id',
    ];

    protected $hidden = [
        'key',
        'created_at',
        'updated_at',
    ];


    protected $table = 'sendgrid_keys';

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Notify::class, 'sendgrid_id', 'id');
    }
}
