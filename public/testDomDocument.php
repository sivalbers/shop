<?php
return;
if (class_exists('DOMDocument')) {
    echo "DOMDocument ist verfügbar!";
} else {
    echo "DOMDocument fehlt! Möglicherweise ist libxml nicht korrekt installiert.";
}
?>
