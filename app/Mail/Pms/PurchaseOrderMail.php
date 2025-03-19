<?php

namespace App\Mail\Pms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;


    public $purchaseOrder='';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $title="Purchase Order Invoice";
        $purchaseOrder=$this->purchaseOrder;
        return $this->view('pms.backend.mail.purchase-order-mail',compact('purchaseOrder','title'));
    }
}
