<?php

namespace App\Models;

use App\Enums\OutboundMessageChannel;
use App\Enums\OutboundMessageStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OutboundMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'channel',
        'status',
        'recipient_type',
        'recipient_id',
        'recipient_identifier',
        'subject',
        'content',
        'html_content',
        'attachments',
        'template_data',
        'template_name',
        'event_type',
        'reservation_id',
        'guest_id',
        'payment_id',
        'scheduled_at',
        'sent_at',
        'failed_at',
        'error_message',
        'retry_count',
        'provider',
        'provider_message_id',
        'provider_response',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'channel' => OutboundMessageChannel::class,
            'status' => OutboundMessageStatus::class,
            'attachments' => 'array',
            'template_data' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
            'retry_count' => 'integer',
            'provider_response' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }
}
