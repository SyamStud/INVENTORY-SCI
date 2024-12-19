<?php

namespace App\Console\Commands;

use App\Mail\DocumentExpiredReminderMail;
use App\Models\Permit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class sendDocumentReminder extends Command
{
    protected $signature = 'send:document-reminder';
    protected $description = 'Memeriksa tanggal dan mengirim email jika melebihi batas';

    public function handle()
    {
        $expiredDocuments = Permit::orderBy('due_date', 'asc')->limit(10)->get();

        foreach ($expiredDocuments as $expiredDocument) {
            $targetDate = Carbon::parse($expiredDocument->due_date);
            $today = Carbon::now();

            if ($today->greaterThan($targetDate)) {
                
                $tujuan = "syamchai.dev@gmail.com";
                Mail::to($tujuan)->send(new DocumentExpiredReminderMail($expiredDocument));
            }
        }
    }
}
