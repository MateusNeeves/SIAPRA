<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

/** Work-only verifier: its result must never authorize authentication. */
class SenhaFicticia
{
    public function hash(): string
    {
        // Fixed salt/digest; parameters follow the configured hasher's work factor.
        // Changing parameters need not preserve a known plaintext: this is never a credential.
        return match (config('hashing.driver')) {
            'bcrypt' => sprintf('$2y$%02d$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', config('hashing.bcrypt.rounds')),
            'argon', 'argon2id' => sprintf('$%s$v=19$m=%d,t=%d,p=%d$c2lhcHJhLWZpeGVkLXNhbHQ$N7YIR3h6ipGmrxZxdL2RbACjU9zWMwL9OAZdQWcjnvk',
                config('hashing.driver') === 'argon' ? 'argon2i' : 'argon2id',
                config('hashing.argon.memory'), config('hashing.argon.time'), config('hashing.argon.threads')),
            default => throw new \RuntimeException('Hasher de autenticação não suportado.'),
        };
    }

    public function check(#[\SensitiveParameter] string $password): void
    {
        Hash::check($password, $this->hash());
    }
}
