<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReceiptEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $view;
    protected $data;
    protected $subject;

    /**
     * Create a new job instance.
     */
    public function __construct($to, $view, $data, $subject)
    {
        $this->to = $to;
        $this->view = $view;
        $this->data = $data;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Send the email
        Mail::send($this->view, $this->data, function ($message) {
            $message->to($this->to)->subject($this->subject);
            $message->from(config('mail.mailers.smtp.username'), 'Perfecto Pizzas');
        });
    }
}
