@if(isset($data['is_advocate']))
    <p>Olá {{ $data['advocate_name'] }}!</p>
    <p>Você está recebendo este e-mail pois o seu cliente <b>{{ $data['client_name'] }}</b> <u>cancelou</u> a reunião agendada para o dia <b>{{ $data['date'] }} ás {{ $data['hour'] }}.</b>
    <p>Atenciosamente,<p> <p>Sistema Advoguez</p>
@else
    <p>Olá {{ $data['client_name'] }}!</p>
    <p>Você está recebendo este e-mail pois a sua reunião agendada para o dia <b>{{ $data['date'] }} ás {{ $data['hour'] }}</b>
    foi <u>cancelada</u> pelo seu advogado.</p>
    <p>Caso tenha alguma dúvida, gentileza entrar em contato com o mesmo.</p>
@endif
