<!DOCTYPE text>
<html>
<head>
    <title>Bestellbestätigung</title>

</head>
<body>
    <p>Sehr geehrte Damen und Herren,</p>
    <p>vielen Dank für Ihre Bestellung im Online-Shop der Firma Sieverding Besitzunternehmen.</p><br>

    <p>Sobald die Ware versandbereit ist, erhalten Sie den entsprechenden Lieferschein per eMail.</p>

    <p>wir Wünschen Frohe Feiertage und verbleiben</p><br>
    mit freundlichen Grüßen<br>
    Sieverding Besitzunternehmen</p>
    @if (count($order['nachrichten']) > 0)
        <span style="text-decoration: underline;"><a href="#nachrichten">>> Bitte beachten Sie auch unsere aktuellen Informationen am Ende dieser Mail! << </a></span>
    @endif
    <br><br>



    <div style="font-weight: bold; font-size: large;">Ihre bestellten Artikel:</div><br>   <!-- ##### Überschrift ##### -->
    <table style=" width: 800px; ">
        <tr style=" border-bottom: 1px solid gray;">

            <td align="left" style="width: 15%; padding-left: 3px; font-weight: bold; ">Artikelnummer</td>
            <td align="left" style="width: 40%; font-weight: bold; ">Bezeichnung</td>
            <td align="right" style="width: 10%; font-weight: bold; ">Menge</td>
            <td align="left" style="width: 5%; font-weight: bold; padding-left: 3px; ">Einheit</td>
            <td align="right" style="width: 15%; font-weight: bold; ">Einzelpreis</td>
            <td align="right" style="width: 15%; padding-right: 3px; font-weight: bold; background-color: #f3f3f3;">Gesamtpreis</td>
        </tr>
        @foreach ($order['bestellungPos'] as $pos)
        <tr>
            <td align="left" style="width: 15%; padding-left: 3px;">{{ $pos->artikelnr }}</td>
            <td align="left" style="width: 40%">{{ $pos->artikel->bezeichnung }}</td>
            <td align="right" style="width: 10%">{{ formatMenge($pos->menge) }}</td>
            <td align="left" style="width: 5%; padding-left: 3px;">{{ $pos->artikel->einheit }}</td>
            <td align="right" style="width: 15%">{{ formatPreis( $pos->epreis ) }} €</td>
            <td align="right" style="width: 15%; padding-right: 3px; background-color: #f3f3f3;">{{ formatGPreis( $pos->gpreis ) }} €</td>
        </tr>

        @endforeach
        <tr syle="">
            <td align="right" colspan="5" style="font-weight: bold; border-top: 1px solid gray;">Gesamtbetrag zzgl. 19% MwSt.:</td>
            <td align="right" colspan="1" style="font-weight: bold;padding-right: 3px; border-top: 1px solid gray; background-color: #f3f3f3;">{{ formatGPreis ($order['bestellung']->gesamtbetrag ) }} €</td>
        </tr>
    </table>
<br>
<br>
    <table style="width: 800px; ">
        <tr>
            <td colspan="2">
                <div style="font-weight: bold;  font-size: large;">Ihre hinterlegten Informationen:</div>  <!-- ##### Überschrift ##### -->
            </td>
        </tr>

        <tr>
            <td>
                Kundenbestellnr.:
            </td>
            <td>
                @if ($order['bestellung']->kundenbestellnr) {{ $order['bestellung']->kundenbestellnr }} @else Keine Angabe @endif
            </td>
        </tr>
        <tr>

            <td>
                Kommission:
            </td>
            <td>
                @if ($order['bestellung']->kommission) {{ $order['bestellung']->kommission }} @else Keine Angabe @endif
            </td>
        </tr>
        <tr>
            <td>
                Bemerkung:
            </td>
            <td>
                @if ($order['bestellung']->bemerkung) {!! nl2br($order['bestellung']->bemerkung) !!} @else Keine Angabe @endif
            </td>
        </tr>
        <tr style="margin-bottom: 6px;">
            <td>
                Gewünschtes Lieferdatum:
            </td>
            <td>
                @if ($order['bestellung']->lieferdatum) {{ $order['bestellung']->lieferDatumStr() }} @else Keine Angabe @endif
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                &nbsp;
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div style="font-weight: bold; font-size: large;">Informationen zur Bestellung:</div>  <!-- ##### Überschrift ##### -->
            </td>
        </tr>

        <tr>
            <td>
                @if ($order['bestellung']->rechnungsadresse === $order['bestellung']->lieferadresse)
                    <div>Rechnungs- und Lieferadresse (falls Lieferung vereinbart ist)</div>
                @else
                    <div>Rechnungsadresse:</div>
                    <div>Lieferadresse:</div>
                @endif
            </td>
            <td>
                @if ($order['bestellung']->rechnungsadresse === $order['bestellung']->lieferadresse)
                    <div>{{ $order['bestellung']->reAdresse->firma1 }} - {{ $order['bestellung']->reAdresse->strasse }} - {{ $order['bestellung']->reAdresse->plz }} {{ $order['bestellung']->reAdresse->stadt }}</div>
                @else
                    <div>{{ $order['bestellung']->reAdresse->firma1 }} - {{ $order['bestellung']->reAdresse->strasse }} - {{ $order['bestellung']->reAdresse->plz }} {{ $order['bestellung']->reAdresse->stadt }}</div>
                    <div>{{ $order['bestellung']->lfAdresse->firma1 }} - {{ $order['bestellung']->lfAdresse->strasse }} - {{ $order['bestellung']->lfAdresse->plz }} {{ $order['bestellung']->lfAdresse->stadt }}</div>
                @endif
            </td>
        </tr>
        <tr>
            <td>Shop-Bestellnr.:</td>
            <td>{{ $order['bestellung']->nr }}</td>
        </tr>
        <tr>
            <td>Kunden-Nr.:</td>
            <td>{{ $order['bestellung']->kundennr }}</td>
        </tr>
        <tr>
            <td>Shop-Login:</td>
            <td>{{ $order['login'] }}</td>
        </tr>
        <tr>
            <td>Zahlungsbedingung:</td>
            <td>Nach Erhalt auf Rechnung ohne Skonto.</td>
        </tr>


    </table>

    <br>
    @if (count($order['nachrichten']) > 0)
    <hr />
    <br>
    <br>
    <div style="font-weight: bold; font-size:large;">Aktuelle Lagerinformationen:</div><br>  <!-- ##### Überschrift ##### -->
    <table id="nachrichten">
        @foreach ($order['nachrichten'] as $nachricht)

        <tr>

            <td>
                @php
                    $color = "";
                    if ($nachricht->prioritaet === 'hoch') {
                     $color = "color: red;";
                    }
                    elseif ($nachricht->prioritaet === 'mittel') {
                    $color = "color: green;";
                    }
                @endphp
            </td>

            <td><!-- Spalte 2 -->
                <div style="font-weight: bold;{{ $color }}">
                    {{ $nachricht->kurztext }}
                </div>
                <div>
                    {{ $nachricht->langtext }}
                </div>
                @if (!empty($nachricht->links))
                    <div>
                        @php
                            $links = $nachricht->getLinksArray();
                        @endphp
                        @if (count($links) > 1)
                            Links:
                            @foreach ( $links as $link )
                                <div>
                                    <span class="color: green;">
                                        <a href="{{ $link['link'] }}" target="_blank">{{ $link['beschreibung'] }}</a>
                                    </span>
                                </div>
                            @endforeach
                        @else
                            Link: <span class="color: green;">
                                <a href="{{ $links[0]['link'] }}" target="_blank">{{ $links[0]['beschreibung'] }}</a>
                            </span>
                        @endif
                    </div>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif
    <div style="font-size: 1px;"> <a href="https://netzmaterialonline.de/unsubscribe">unsubscribe</a></div>
</body>
</html>
