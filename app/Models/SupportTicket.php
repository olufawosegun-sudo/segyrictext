<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';

    protected $fillable = ['ticket_number', 'user_id', 'assigned_admin_id', 'subject', 'priority', 'status', 'resolved_at'];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function assignedAdmin() { return $this->belongsTo(User::class, "assigned_admin_id"); }

    public function messages() { return $this->hasMany(SupportTicketMessage::class); }
}
