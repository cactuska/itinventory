<p>Új eszköz regisztrálva:</p>

Leírás {!! $record->description !!}<br>
S/N: {!! $record->serial !!}<br>
Beszállító: {!! $record->supplyer !!}<br>
Érték: {!! $record->price !!}<br>
Vásárlás dátuma: {!! $record->purdate !!}<br>
Telephely: {!! $record->loc->compcode !!}<br>
Megjegyzés: {!! $record->note !!}<br><br>

Üdvözlettel<br>
IT Osztály <br>
Rögzítette: {!! $user !!}