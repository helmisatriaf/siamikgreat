<?php

namespace App\Jobs;

use App\Mail\PaymentSuccessMail;
use App\Models\statusInvoiceMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceived implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $email, $mailData, $subject, $pdfBill;
    public $tries = 5;
    /**
     * Create a new job instance.
     */
    public function __construct($email, $mailData, $subject, $pdfBill)
    {
        $this->email = $email;
        $this->mailData = $mailData;
        $this->subject = $subject;
        $this->pdfBill = $pdfBill;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         info('payment clicked running 2');
         $pdf = app('dompdf.wrapper');
         $pdf->loadView('components.bill.pdf.paid-pdf', ['data' => $this->pdfBill])->setPaper('a4', 'portrait');
         $pdfReport = null;
         if($this->pdfBill->installment){
                     
               $pdfReport = app('dompdf.wrapper');
               $pdfReport->loadView('components.bill.pdf.installment-pdf', ['data' => $this->pdfBill])->setPaper('a4', 'portrait');
         }

         Mail::to($this->email[0])->cc($this->email[1])->send(new PaymentSuccessMail($this->mailData, $this->subject, $pdf, $pdfReport));

         $bill = statusInvoiceMail::create([
            'status' => true,
            'bill_id' => $this->pdfBill->id,
            'is_paid' => true,
         ]);

         info('bill created ' . $bill);
   }

   public function failed(\Exception $exception) :void
   {
        statusInvoiceMail::create([
            'status' => false,
            'bill_id' => $this->pdfBill->id,
            'is_paid' => true,
        ]);
   }
}