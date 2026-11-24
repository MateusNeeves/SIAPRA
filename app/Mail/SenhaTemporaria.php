<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Mail\Mailable;

class SenhaTemporaria extends Mailable
{
    public function __construct(public string $senha, public CarbonInterface $expires) {}

    public function build(): static
    {
        return $this->subject('SIAPRA — Recuperação de acesso')->view('emails.senha-temporaria');
    }
}
