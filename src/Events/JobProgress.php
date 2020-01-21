<?php namespace Cupparis\App\Events;

use App\Events\Event;

use Illuminate\Queue\SerializesModels;

class JobProgress extends Event {

	use SerializesModels;

    public $jobId;
    public $progress;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($jobId,$progress)
	{
		$this->jobId = $jobId;
        $this->progress = $progress;
	}

}
