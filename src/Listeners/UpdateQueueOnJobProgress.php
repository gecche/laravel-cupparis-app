<?php namespace Cupparis\App\Listeners;

use App\Events\JobProgress;

use App\Models\Activityqueue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueued;
use Illuminate\Support\Facades\Log;

class UpdateQueueOnJobProgress {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  JobProgress  $event
	 * @return void
	 */
	public function handle($jobId,$progress)
	{
        $acQueue = Activityqueue::find($jobId); //new Activityqueue ();
        $acQueue->progress = $progress;
        $acQueue->save();		//

        $file = fopen(storage_path('files/queues/progress_'.$jobId), 'w+');
        fwrite($file,cupparis_json_encode($acQueue->toArray()));
        fclose($file);

	}

}
