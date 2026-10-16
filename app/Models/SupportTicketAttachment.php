<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketAttachment extends Model
{
    use HasFactory;

    protected $table = 'support_ticket_attachments';

    protected $fillable = ['support_ticket_message_id', 'file_name', 'file_path', 'file_size', 'mime_type'];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function message() { return $this->belongsTo(SupportTicketMessage::class, "support_ticket_message_id"); }
}
