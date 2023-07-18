<?php

namespace App\Listeners;

use App\Events\StudentRegisterEvent;
use App\Models\StudentBalance;

class StudentRegisterListener
{
    /**
     * Create the event listener.
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
     * @param  StudentRegisterEvent  $event
     * @return void
     */
    public function handle(StudentRegisterEvent $event)
    {
        StudentBalance::create(['student_id' => $event->student->id]);
    }
}
