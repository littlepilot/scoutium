@component('mail::message')
Scoutium'a davet edildiniz, aşağıdaki kodu kullanarak kayıt olabilirsiniz.

<strong>{{ $invitation->invite_code }}</strong>>

Teşekkürler,<br>
{{ config('app.name') }}
@endcomponent
