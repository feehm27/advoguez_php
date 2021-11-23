<p>Olá {{ $user['name'] }}!</p>

<p>Você está recebendo este e-mail pois solicitou a recuperação de senha no advoguez.</p>

<p>
<a href="{{ env('ADVOGUEZ_FRONT').'/change-password/'.$user['token']}}">Clique aqui</a> para cadastrar uma nova senha.
</p>