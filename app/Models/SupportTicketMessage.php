<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketMessage extends Model
{
    use HasFactory;

    protected $table = 'support_ticket_messages';

    protected $fillable = ['support_ticket_id', 'sender_id', 'message'];

    public function ticket() { return $this->belongsTo(SupportTicket::class, "support_ticket_id"); }

    public function sender() { return $this->belongsTo(User::class, "sender_id"); }

    public function attachments() { return $this->hasMany(SupportTicketAttachment::class); }
}
