<p>{!! $employeename !!} részére a következő IT eszköz lett kiadva:</p>
@foreach($items as $item)
    Típus: {!! $item->description !!}<br>
    S/N: {!! $item->serial !!}<br><br>
@endforeach

Üdvözlettel<br>
IT Osztály <br>
Rögzítette: {!! $user !!}