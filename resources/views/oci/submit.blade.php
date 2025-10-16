<div class="container">
    <h1>Erfolgreich übermittelt</h1>
    <p>Die Artikel wurden an Mercateo übergeben.</p>
    <p>Dieses Fenster schließt sich automatisch...</p>
</div>
<pre>
    <br>hookUrl:</br>
    {{ $hookUrl }}

    <br>chartData:</br>
    {!! json_encode($cartData, JSON_PRETTY_PRINT) !!}
</pre>

<script>
    setTimeout(function() {
 //       window.close();
        // Fallback, falls window.close() nicht funktioniert
        // window.location.href = 'about:blank';
    }, 2000);
</script>
