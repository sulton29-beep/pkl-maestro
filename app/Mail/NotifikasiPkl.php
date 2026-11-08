<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiPkl extends Mailable
{
    use Queueable, SerializesModels;

    public string $judul;
    public string $pesan;
    public ?string $namaPenerima;
    public ?string $linkAksi;
    public ?string $labelAksi;
    public array $dataTambahan;

    public function __construct(
        string $judul,
        string $pesan,
        ?string $namaPenerima = null,
        ?string $linkAksi = null,
        ?string $labelAksi = null,
        array $dataTambahan = []
    ) {
        $this->judul = $judul;
        $this->pesan = $pesan;
        $this->namaPenerima = $namaPenerima;
        $this->linkAksi = $linkAksi;
        $this->labelAksi = $labelAksi ?? 'Buka Aplikasi';
        $this->dataTambahan = $dataTambahan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[PKL Maestro] ' . $this->judul,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notifikasi-pkl',
        );
    }
}