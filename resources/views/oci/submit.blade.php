<!DOCTYPE html>
<html>
<head>
    <title>Zurück zu Mercateo...</title>
</head>
<body>
    <div class="container">
        <h1>Daten werden übermittelt...</h1>
        <p>Bitte warten Sie einen Moment.</p>
    </div>

    <!-- Debug-Ausgabe (später entfernen)
    <pre style="display: none;">
        <br>hookUrl:</br>
        {{ $hookUrl }}

        <br>cartData:</br>
        {!! json_encode($cartData, JSON_PRETTY_PRINT) !!}
    </pre>
    -->
    
    <!-- Unsichtbares Formular für OCI-Daten -->
    <form id="ociForm" method="POST" action="{{ $hookUrl }}" style="display: none;">
        @foreach($cartData as $fieldName => $fieldValues)
            @if(is_array($fieldValues))
                @foreach($fieldValues as $index => $value)
                    @if(is_array($value))
                        {{-- Für LONGTEXT-Felder die Arrays sind --}}
                        @foreach($value as $subValue)
                            <input type="hidden" name="{{ $fieldName }}[]" value="{{ $subValue }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $fieldName }}[{{ $index }}]" value="{{ $value }}">
                    @endif
                @endforeach
            @else
                <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValues }}">
            @endif
        @endforeach
    </form>

    <script>
        // Formular sofort absenden
        document.getElementById('ociForm').submit();
    </script>
</body>
</html>
