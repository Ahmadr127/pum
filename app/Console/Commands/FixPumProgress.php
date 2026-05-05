<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PumRequest;

class FixPumProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pum:fix-progress {code? : Kode / No Surat yang ingin diperbaiki} {--all : Perbaiki semua pengajuan yang aktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbaiki progress workflow PUM yang terskip atau tidak konsisten';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $code = $this->argument('code');
        $all = $this->option('all');

        if (!$code && !$all) {
            $this->error('Harap masukkan Kode / No Surat atau gunakan opsi --all');
            return 1;
        }

        $query = PumRequest::query();

        if ($code) {
            $query->where(function($q) use ($code) {
                $q->where('code', $code)->orWhere('no_surat', $code);
            });
        } else {
            $query->whereIn('status', [
                PumRequest::STATUS_PENDING,
                PumRequest::STATUS_APPROVED
            ]);
        }

        $requests = $query->get();

        if ($requests->isEmpty()) {
            $this->warn('Tidak ditemukan pengajuan yang cocok.');
            return 0;
        }

        $this->info('Menyinkronkan ' . $requests->count() . ' pengajuan...');

        $bar = $this->output->createProgressBar($requests->count());
        $bar->start();

        foreach ($requests as $request) {
            $this->comment("Memproses: {$request->no_surat}");
            $request->syncWorkflowProgress();
            $this->info("Berhasil disinkronkan: {$request->no_surat}");
            $bar->advance();
        }



        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Progress telah disinkronkan.');

        return 0;
    }
}
