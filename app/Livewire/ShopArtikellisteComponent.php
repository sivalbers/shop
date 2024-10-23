<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Artikel;
use App\Models\Bestellung;
use App\Models\Position;
use App\Models\Config;

use Exception;
use Livewire\Attributes\On;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Log;
use App\Enums\Tab;

use App\Models\Warengruppe;

class ShopArtikellisteComponent extends Component
{

    use WithPagination;


    public $myArtikels = [];
    public $quantities;
    public $selectedWarengruppe = null;
    public $selectedWarengruppeBezeichung = '';
    public $showForm ;

    public $selectedTab = Tab::arWG;

    public $listKurz = true;

    const CONFIG_LISTKURZ = 'listKurz';


    public function mount($quantities)
    {
        $this->listKurz =  Config::userString(self::CONFIG_LISTKURZ) === 'true';
        $this->myArtikels = collect();
    }

    public function checkArtikelType()
    {
        if ($this->myArtikels instanceof LengthAwarePaginator || $this->myArtikels instanceof Paginator || $this->myArtikels instanceof \Illuminate\Database\Eloquent\Collection ) {
            return true;
        } elseif ($this->myArtikels instanceof Collection) {
            return false;
        } else {
            return false;
        }
    }

    #[On('showArtikelWG')]
    public function selectWarengruppe($wgnr, $sortiment)
    {


        if ($wgnr) {

            $this->selectedTab = Tab::arWG;

            $this->quantities = array();

            $sortimentArray = explode(' & ', $sortiment);

            $warengruppe = Warengruppe::where('wgnr', $wgnr)->first();

            if ($warengruppe) {
                $this->selectedWarengruppeBezeichung = $warengruppe->bezeichnung;
            }

            $this->myArtikels = Artikel::join('artikel_sortimente as a_s', 'artikels.artikelnr', '=', 'a_s.artikelnr')
                ->where('artikels.wgnr', $wgnr)
                ->whereIn('a_s.sortiment', $sortimentArray)
                ->select('artikels.*')
                ->get();

            // Initialisiere das quantities-Array mit Standardwerten (z.B. 0)
            foreach ($this->myArtikels as $artikel) {

                $this->quantities[(string)$artikel->artikelnr] = [
                    'menge' => 0,
                    'id' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'epreis' => $artikel->vkpreis,
                    'steuer' => $artikel->steuer,
                    'bestand' =>  rand(0, 10),
                ];

            }


            $this->selectedWarengruppe = $wgnr;
        } else {

            $this->selectedWarengruppeBezeichung = '';
            $this->myArtikels = Artikel::join('artikel_sortimente as a_s', 'artikels.artikelnr', '=', 'a_s.artikelnr')
                ->where('artikels.wgnr', '')
                ->select('artikels.*')
                ->get();

            foreach ($this->myArtikels as $artikel) {
                $this->quantities[(string)$artikel->artikelnr] = [
                    'menge' => 0,
                    'id' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'epreis' => $artikel->vkpreis,
                    'steuer' => $artikel->steuer,
                    'bestand' =>  rand(0, 10),
                ];

            }

            $this->selectedWarengruppe = null;
        }

    }

    function findMengeByArtikelnummer(&$artikelArray, $artikelnummer) {
        foreach ($artikelArray as &$artikel) {
            if ($artikel['artikelnummer'] === $artikelnummer) {
                $artikel['artikelnummer'] = 'x';
                return $artikel['menge'];
            }
        }

        // Falls die Artikelnummer nicht gefunden wird, kann null zurückgegeben werden
        return null;
    }

    #[On('showArtikelSchnellerfassung')]
    public function selectSchnellerfassung($artikelArray, $sortiment)
    {

        $this->selectedTab = Tab::arSchnellerfassung;

        $this->quantities = array();

        $artikelStr = '';

        foreach ($artikelArray as $art){
            $artikelStr = $artikelStr . $art['artikelnummer']. ', ';
        }

        // dd($artikelStr);
        $artikelnummern = array_column($artikelArray, 'artikelnummer');
        $sortimentArray = explode(' & ', $sortiment);


        $qu = Artikel::join('artikel_sortimente as a_s', 'artikels.artikelnr', '=', 'a_s.artikelnr')
                ->whereIn('artikels.artikelnr', $artikelnummern)
                ->whereIn('a_s.sortiment', $sortimentArray)
                ->select('artikels.*');

        $artikellist = $qu->get();

        $this->myArtikels = array();
        foreach ($artikelArray as $art){

            $xx = $artikellist->firstWhere('artikelnr', $art['artikelnummer']);
            if ($xx){
                $this->myArtikels[] = $xx;
            }

        }
        //dd($this->myArtikels);

        $this->quantities = array();

        foreach ($this->myArtikels as $art){
            //Log::info(' ', []);
            //Log::info('Suche Artikel: ', [$art->artikelnr]);
            //Log::info('Vorher ', $artikelArray);
            $this->quantities[(string)$art->artikelnr] = [
                'menge' => $this->findMengeByArtikelnummer($artikelArray, $art->artikelnr),
                'id' => 0,
                'artikelnr' => $art->artikelnr,
                'epreis' => $art->vkpreis,
                'steuer' => $art->steuer,
                'bestand' =>  rand(0, 10),
            ];
            //Log::info('Nachher', $artikelArray);

        }
        //dd($this->quantities);
    }


    public function render()
    {
        // Log::info('ShopArtikellisteComponent - render', [ $this->myArtikels ]);
/*
        $ttype = $this->checkArtikelType();
        if ( !$ttype){
            //Log::info('ShopArtikellisteComponent - render CheckArtikelType = ', [ $ttype ]);
            $this->selectWarengruppe('', 'EWE');
        }
        else{
             // Log::info('ShopArtikellisteComponent - render CheckArtikelType = ', [ $ttype ]);
        }
*/
        $artikels = $this->myArtikels;
        return view('livewire.shop.shopartikelliste', [ 'artikels' => $artikels ]);

        if (!$this->listKurz){
            return view('livewire.shop.shopartikelliste', [ 'artikels' => $artikels ]);
        }
        else{
            return view('livewire.shop.shopartikelliste_short', [ 'artikels' => $artikels ]);
        }

    }

    public function showArtikel($artikelnr){
        $this->dispatch('showArtikel' , ['artikelnr' => $artikelnr ]);
    }

    #[On('updateQuantityPos')]
    public function updateQuantityPos($artikelnr, $quantity)
    {
        Log::info('In updateQuantityPos', [ $artikelnr, $quantity ]);
        try{
            if ($quantity >= 0) {
                $this->quantities[$artikelnr]['menge'] = $quantity;
                Log::info('Neue Menge ', [ $this->quantities[$artikelnr] ]);
                //$this->dispatch('updateQuantity' , $artikelnr, $quantity);
            }
        } catch (\Exception $e) {
            return log::error ( 'Fehler in updateQuantityPos: ' , [ $e->getMessage()]);
        }
    }

    public function InBasket()
    {
        $bestellung = Bestellung::getBasket();
        if ($bestellung) {
            $bestellung->datum = now();
            $bestellung->save();

            // Jetzt kannst du direkt auf die Mengen zugreifen
            $quantities = $this->quantities;
    //             dd($quantities); // Zum Debuggen

            foreach ($quantities as $artikelnr => $data) {
                if ($data['menge'] >0) {

                    if ($data['id'] == 0){
                        $pos = Position::Create([
                            'bestellnr' => $bestellung->nr,
                            'artikelnr' => $data['artikelnr'],
                            'menge' => $data['menge'],
                            'epreis' => $data['epreis'],
                            'gpreis' => $data['menge'] * $data['epreis'],
                            'steuer' => $data['steuer'],
                            'sort' => 0,
                        ]);

                        Log::info('Before Bestellnr, artikelnr, menge, gpreis', [ $bestellung->nr, $data['artikelnr'], $quantities[$artikelnr]['menge'], $data['epreis'], $data['steuer'] ]);

                            $quantities[$artikelnr]['menge'] = 0;

                        Log::info('After Bestellnr, artikelnr, menge, gpreis', [ $bestellung->nr, $data['artikelnr'], $quantities[$artikelnr]['menge'], $data['epreis'], $data['steuer'] ]);
                    }
                }

            }

            $bestellung->save();



            //dd($quantities); // Zum Debuggen


            $this->dispatch('updateNavigation');
            $this->dispatch('basket-cleared');

        }
    }

    public function toggle_listKurz(){
        $this->listKurz = !$this->listKurz;

        Config::setUserString(self::CONFIG_LISTKURZ, $this->listKurz ? 'true' : 'false');

    }

}
