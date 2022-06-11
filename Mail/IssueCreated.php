<?php

namespace App\Mail;

use \App\Models\Issue;
use App\Utils\Helpers\FactoringHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueCreated extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Issue */
    protected $issue;

    /**
     * IssueAssigned constructor.
     * @param Issue $issue
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('mails.issue.created')
            ->with([
                'issue' => $this->issue,
                'userName' => $this->issue->user->name,
                'issueNumber' => $this->issue->id,
                'issueTitle' => $this->issue->title,
                'issueDescription' => FactoringHelper::makeShort($this->issue->description)
            ]);
    }
}
