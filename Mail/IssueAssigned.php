<?php

namespace App\Mail;

use \App\Models\Issue;
use App\Utils\Helpers\FactoringHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueAssigned extends Mailable
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
            ->view('mails.issue.assigned')
            ->with([
                'issue' => $this->issue,
                'executorName' => $this->issue->executor->name,
                'issueNumber' => $this->issue->id,
                'issueTitle' => $this->issue->title,
                'issueDescription' => FactoringHelper::makeShort($this->issue->description)
            ]);
    }
}
