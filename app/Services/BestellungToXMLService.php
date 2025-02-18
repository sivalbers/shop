<?php

namespace App\Services;

class BestellungToXMLService
{
    private $dom;
    private $root;

    public function __construct($best)
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;


        // **<Request> als Wurzelelement mit Attributen erstellen**
        $this->root = $this->dom->createElement('Request');
        $this->root->setAttribute('Name', 'ORDERCREATIONEXTENDED');
        $this->root->setAttribute('Mandant', '');
        $this->root->setAttribute('User', $best->kundennr);
        $this->root->setAttribute('Sender', 'MULTISHOP');
        $this->dom->appendChild($this->root);


        $params = $this->dom->createElement('Params');
        $this->root->appendChild($params);

        // Warenkorb
        $warenkorb = $this->dom->createElement('Warenkorb');
        $params->appendChild($warenkorb);

        $warenkorb->appendChild($this->createElement('Auftragsnummer', $best->nr));

        $positionen = $best->positionen;

        foreach ($positionen as $pos){
            $zeile = $this->dom->createElement('Zeile');
            $warenkorb->appendChild($zeile);
            $zeile->appendChild($this->createElement('Artikelnummer', $pos->artikelnr));
            $zeile->appendChild($this->createElement('Menge', $pos->menge));
            $zeile->appendChild($this->createElement('VKPreis', $pos->epreis));
        }

        // Lieferadresse
        $lf = $best->lfAdresse;
        $lieferadresse = $this->dom->createElement('Lieferadresse');
        $params->appendChild($lieferadresse);
        $lieferadresse->appendChild($this->createElement('LieferadresseName1', $lf->firma1));
        $lieferadresse->appendChild($this->createElement('LieferadresseName2', $lf->firma2));
        $lieferadresse->appendChild($this->createElement('LieferadresseName3', $lf->firma3));
        $lieferadresse->appendChild($this->createElement('LieferadresseStrasse', $lf->strasse));
        $lieferadresse->appendChild($this->createElement('LieferadressePlz', $lf->plz));
        $lieferadresse->appendChild($this->createElement('LieferadresseOrt', $lf->stadt));
        $lieferadresse->appendChild($this->createElement('LieferadresseLand', $lf->land));

        // Rechnungsadresse
        $re = $best->reAdresse;
        $rechadresse = $this->dom->createElement('Rechadresse');
        $params->appendChild($rechadresse);
        $rechadresse->appendChild($this->createElement('RechName1',     $re->firma1));
        $rechadresse->appendChild($this->createElement('RechName2',     $re->firma2));
        $rechadresse->appendChild($this->createElement('RechName3',     $re->firma3));
        $rechadresse->appendChild($this->createElement('RechAdresse1',  $re->strasse));
        $rechadresse->appendChild($this->createElement('RechPlz',       $re->plz));
        $rechadresse->appendChild($this->createElement('RechOrt',       $re->ort));
        $rechadresse->appendChild($this->createElement('RechLand',      $re->land ));

        // Zusatzinfos
        $zusatzinfos = $this->dom->createElement('Zusatzinfos');
        $params->appendChild($zusatzinfos);
        $zusatzinfos->appendChild($this->createElement('Zusatzinfo1', $best->kommission));
        $zusatzinfos->appendChild($this->createElement('Zusatzinfo2', $best->kundennr));
        $zusatzinfos->appendChild($this->createElement('Zusatzinfo3', $best->lieferdatum));
        $zusatzinfos->appendChild($this->createElement('Zusatzinfo4', $best->kundenbestellnr));
        //$zusatzinfos->appendChild($this->createElement('Zusatzinfo5', ''));
        // $zusatzinfos->appendChild($this->createElement('Zusatzinfo6', $data['Zusatzinfo6']));


        // Bemerkung
        $params->appendChild($this->createElement('Bemerkung', $best->bemerkung));
        $params->appendChild($this->createElement('Lieferdatum', $best->lieferdatum->format('d.m.y')));


        /*
        // Metadaten
        $params->appendChild($this->createElement('Herkunft', $data['Herkunft']));
        $params->appendChild($this->createElement('Auftragsparameter', $data['Auftragsparameter']));
        $params->appendChild($this->createElement('AccountName', $data['AccountName']));
        */
    }

    private function createElement($name, $value)
    {
        $element = $this->dom->createElement($name);
        $element->appendChild($this->dom->createTextNode($value));
        return $element;
    }

    public function getXML()
    {
        return $this->dom->saveXML();
    }

}
