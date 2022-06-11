<?php

namespace App\Listeners;

use App\Events\IssueUpdated as IssueUpdatedEvent;
use App\Mail\IssueAssigned as IssueAssignedMail;
use App\Models\Issue;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendEmailIssueUpdatedToUsers
 * @package App\Listeners
 */
class SendEmailIssueUpdatedToUsers
{
    /**
     * @param IssueUpdatedEvent $event
     */
    public function handle(IssueUpdatedEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->issue;
        $changes = $issue->getChanges();

        if (isset($changes['executor_id'])) {
            Mail::to($issue->executor->email)->queue(new IssueAssignedMail($issue));
        }
    }
}
