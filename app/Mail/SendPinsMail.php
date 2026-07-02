<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPinsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $pins; // array or collection of pins
    public $year;

    /**
     * Create a new message instance.
     *
     * @param string $customerName
     * @param array|\Illuminate\Support\Collection $pins  // each item: ['pin' => 'xxxx', 'amount' => 100]
     */
    public function __construct($customerName, $pins)
    {
        $this->customerName = $customerName;
        $this->pins = $pins;
        $this->year = date('Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your PIN Codes')
            ->view('mail.pins'); // Blade view created next
    }
}
