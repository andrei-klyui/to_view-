<?php

namespace App\Listeners;

use App\Events\IssueCreated as IssueCreatedEvent;
use App\Mail\IssueCreated as IssueCreatedMail;
use App\Mail\IssueAssigned as IssueAssignedMail;
use App\Models\Issue;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendEmailIssueToUsers
 * @package App\Listeners
 */
class SendEmailIssueToUsers
{
    /**
     * @param IssueCreatedEvent $event
     */
    public function handle(IssueCreatedEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->issue;

        Mail::to($issue->executor->email)->queue(new IssueAssignedMail($issue));
        Mail::to($issue->user->email)->queue(new IssueCreatedMail($issue));
    }
}
