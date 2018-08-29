<p>{!! $employeename !!}-tól a következő IT eszközök lettek visszavéve:</p>
@foreach($items as $item)
    Típus: {!! $item->description !!}<br>
    S/N: {!! $item->serial !!}<br><br>
@endforeach

Üdvözlettel<br>
IT Osztály <br>
Rögzítette: {!! $user !!}