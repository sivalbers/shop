Sehr geehrte Damen und Herren,

vielen Dank für Ihre Bestellung im Online-Shop der Firma Sieverding Besitzunternehmen.

Sobald die Ware versandbereit ist, erhalten Sie den entsprechenden Lieferschein per eMail.

Wir wünschen frohe Feiertage und verbleiben

mit freundlichen Grüßen
Sieverding Besitzunternehmen

@if (count($order['nachrichten']) > 0)
>> Bitte beachten Sie auch unsere aktuellen Informationen am Ende dieser Mail! <<
@endif


Ihre bestellten Artikel:
---------------------------------------------
Artikelnummer    Bezeichnung                   Menge    Einheit    Einzelpreis    Gesamtpreis
@foreach ($order['bestellungPos'] as $pos)
{{ $pos->artikelnr }}    {{ $pos->artikel->bezeichnung }}    {{ formatMenge($pos->menge) }}    {{ $pos->artikel->einheit }}    {{ formatPreis($pos->epreis) }} €    {{ formatGPreis($pos->gpreis) }} €
@endforeach
---------------------------------------------
Gesamtbetrag zzgl. 19% MwSt.: {{ formatGPreis($order['bestellung']->gesamtbetrag) }} €


Ihre hinterlegten Informationen:
---------------------------------------------
Kundenbestellnr.: @if ($order['bestellung']->kundenbestellnr) {{ $order['bestellung']->kundenbestellnr }} @else Keine Angabe @endif
Kommission: @if ($order['bestellung']->kommission) {{ $order['bestellung']->kommission }} @else Keine Angabe @endif
Bemerkung: @if ($order['bestellung']->bemerkung) {{ $order['bestellung']->bemerkung }} @else Keine Angabe @endif
Gewünschtes Lieferdatum: @if ($order['bestellung']->lieferdatum) {{ $order['bestellung']->lieferDatumStr() }} @else Keine Angabe @endif


Informationen zur Bestellung:
---------------------------------------------
@if ($order['bestellung']->rechnungsadresse === $order['bestellung']->lieferadresse)
Rechnungs- und Lieferadresse (falls Lieferung vereinbart ist):
{{ $order['bestellung']->reAdresse->firma1 }}, {{ $order['bestellung']->reAdresse->strasse }}, {{ $order['bestellung']->reAdresse->plz }} {{ $order['bestellung']->reAdresse->stadt }}
@else
Rechnungsadresse:
{{ $order['bestellung']->reAdresse->firma1 }}, {{ $order['bestellung']->reAdresse->strasse }}, {{ $order['bestellung']->reAdresse->plz }} {{ $order['bestellung']->reAdresse->stadt }}

Lieferadresse:
{{ $order['bestellung']->lfAdresse->firma1 }}, {{ $order['bestellung']->lfAdresse->strasse }}, {{ $order['bestellung']->lfAdresse->plz }} {{ $order['bestellung']->lfAdresse->stadt }}
@endif

Shop-Bestellnr.: {{ $order['bestellung']->nr }}
Kunden-Nr.: {{ $order['bestellung']->kundennr }}
Shop-Login: {{ $order['login'] }}
Zahlungsbedingung: Nach Erhalt auf Rechnung ohne Skonto.


@if (count($order['nachrichten']) > 0)
---------------------------------------------
Aktuelle Lagerinformationen:
---------------------------------------------
@foreach ($order['nachrichten'] as $nachricht)
- {{ $nachricht->kurztext }}
  {{ $nachricht->langtext }}
  @if (!empty($nachricht->links))
  @php
      $links = $nachricht->getLinksArray();
  @endphp
  @if (count($links) > 1)
  Links:
  @foreach ($links as $link)
  - {{ $link['beschreibung'] }}: {{ $link['link'] }}
  @endforeach
  @else
  Link: {{ $links[0]['beschreibung'] }}: {{ $links[0]['link'] }}
  @endif
  @endif
@endforeach
@endif