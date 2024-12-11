<?php

namespace App\Console\Commands;

use App\Mail\DocumentExpiredReminderMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class sendDocumentReminder extends Command
{
    protected $signature = 'send:document-reminder';
    protected $description = 'Memeriksa tanggal dan mengirim email jika melebihi batas';

    public function handle()
    {
        $expiredDocuments = DB::table('permits')
            ->get();

        foreach ($expiredDocuments as $expiredDocument) {
            $targetDate = Carbon::parse($expiredDocument->due_date);
            $today = Carbon::now();

            // Periksa jika tanggal hari ini melebihi tanggal target
            if ($today->greaterThan($targetDate)) {
                
                // Kirim email
                $tujuan = "";
                Mail::to('syamchai.dev@gmail.com')->send(new DocumentExpiredReminderMail($expiredDocument));
            }
        }
    }
}
