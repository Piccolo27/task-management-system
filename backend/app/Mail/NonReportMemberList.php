<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NonReportMemberList extends Mailable
{
    use Queueable, SerializesModels;

    private Employee $admin;
    private Collection $notReportedMembers;

    /**
     * Create a new message instance.
     */
    public function __construct(Employee $admin, Collection $notReportedMembers)
    {
        $this->notReportedMembers = $notReportedMembers;
        $this->admin = $admin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Non Report Member List',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.non-reported-members',
            with: [
                'admin' => $this->admin,
                'date' => now()->format('Y-m-d'),
                'notReportedMembers' => $this->notReportedMembers,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
