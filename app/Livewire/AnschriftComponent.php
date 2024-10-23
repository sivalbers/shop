<?php

namespace App\Livewire;

use Livewire\WithPagination;

use App\Models\Anschrift;
use Livewire\Component;

class AnschriftComponent extends Component
{

    use WithPagination;

    public $anschriften;
    public $anschrift_id;
    public $kundennr;
    public $usersid = -1;
    public $kurzbeschreibung;
    public $firma1;
    public $firma2;
    public $firma3;
    public $strasse;
    public $plz;
    public $stadt;
    public $land;
    public $standard = false;
    public $art = 'Lieferadresse';

    public $kurzBezeichnungFilter;
    public $firma1Filter;
    public $firma2Filter;
    public $firma3Filter;
    public $artFilter;

    protected $rules = [
        'kundennr' => 'required|integer',
        'usersid' => 'required|integer',
        'kurzbeschreibung' => 'required|string|max:100',
        'firma1' => 'required|string|max:80',
        'firma2' => 'nullable|string|max:80',
        'firma3' => 'nullable|string|max:80',
        'strasse' => 'required|string|max:80',
        'plz' => 'required|string|max:8',
        'stadt' => 'required|string|max:80',
        'land' => 'required|string|max:80',
        'standard' => 'boolean',
        'art' => 'required|in:Lieferadresse,Rechnungsadresse',
    ];

    public function mount()
    {
        $this->anschriften = Anschrift::get();

    }

    public function save()
    {
        $this->validate();

        Anschrift::updateOrCreate(
            ['id' => $this->anschrift_id],
            [
                'kundennr' => $this->kundennr,
                'usersid' => $this->usersid,
                'kurzbeschreibung' => $this->kurzbeschreibung,
                'firma1' => $this->firma1,
                'firma2' => $this->firma2,
                'firma3' => $this->firma3,
                'strasse' => $this->strasse,
                'plz' => $this->plz,
                'stadt' => $this->stadt,
                'land' => $this->land,
                'standard' => $this->standard,
                'art' => $this->art,
            ]
        );

        $this->resetForm();
        $this->anschriften = Anschrift::all();

    }

    public function edit($id)
    {
        $anschrift = Anschrift::findOrFail($id);
        $this->anschrift_id = $anschrift->id;
        $this->kundennr = $anschrift->kundennr;
        $this->usersid = $anschrift->usersid;
        $this->kurzbeschreibung = $anschrift->kurzbeschreibung;
        $this->firma1 = $anschrift->firma1;
        $this->firma2 = $anschrift->firma2;
        $this->firma3 = $anschrift->firma3;
        $this->strasse = $anschrift->strasse;
        $this->plz = $anschrift->plz;
        $this->stadt = $anschrift->stadt;
        $this->land = $anschrift->land;
        $this->standard = $anschrift->standard;
        $this->art = $anschrift->art;
    }

    public function resetForm()
    {
        $this->anschrift_id = null;
        $this->kundennr = '';
        $this->usersid = -1;
        $this->kurzbeschreibung = '';
        $this->firma1 = '';
        $this->firma2 = '';
        $this->firma3 = '';
        $this->strasse = '';
        $this->plz = '';
        $this->stadt = '';
        $this->land = '';
        $this->standard = false;
        $this->art = 'Lieferadresse';
    }

    public function render()
    {
        return view('livewire.anschrift-component');
    }
}
