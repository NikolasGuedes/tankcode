<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $resetUrl;

    public function __construct(Student $student, string $resetUrl)
    {
        $this->student = $student;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Redefinir Senha - Tank Code')
                    ->view('emails.student-reset-password');
    }
}
