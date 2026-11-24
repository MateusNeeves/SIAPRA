<h1>SIAPRA — Recuperação de acesso</h1>
<p>Foi solicitada a recuperação de seu acesso.</p>
<p>Senha temporária: <strong>{{ $senha }}</strong></p>
<p>Válida até {{ $expires->copy()->timezone(config('recuperacao.timezone'))->format('d/m/Y H:i T') }}.</p>
<p>Acesse o SIAPRA e entre com essa senha para definir uma nova senha.</p>
<p>Apenas a recuperação mais recente permanece válida. Sua senha definitiva permanece válida até a conclusão da recuperação. Entrar com ela cancela a recuperação.</p>
<p>Se você não solicitou a recuperação, ignore esta mensagem.</p>
