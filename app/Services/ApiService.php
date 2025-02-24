<?php

namespace App\Services;

use App\Repositories\ArtikelRepository;
use App\Repositories\WarengruppeRepository;
use App\Repositories\UserRepository;
use App\Repositories\ArtikelSortimentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


use App\Models\Artikel;

class ApiService
{

    /*
    artikel         => products         => anlegen einzeln OK, anlegen Masse, ändern, löschen
    warengruppen    => categories       => anlegen einzeln, anlegen Masse, ändern, löschen, lesen alle, lesen einzeln
    sortiment       => product-ranges   => anlegen einzeln, anlegen Masse, ändern, löschen, lesen alle, lesen einzeln
    bestellungen    => orders           => anlegen einzeln, anlegen Masse, ändern, löschen, lesen alle, lesen einzeln
    users           => users            => anlegen einzeln, anlegen Masse, ändern, löschen, lesen alle, lesen einzeln
    Adressen        => address          => anlegen einzeln, anlegen Masse, ändern, löschen, lesen alle, lesen einzeln
    */
    protected $artikelRepository;
    protected $warengruppeRepository;
    protected $userRepository;
    protected $artikelSortimentRepository;

    public function __construct(ArtikelRepository $artikelRepository,
        WarengruppeRepository $warengruppeRepository,
        UserRepository $userRepository,
        ArtikelSortimentRepository $artikelSortimentRepository)
    {
        $this->artikelRepository = $artikelRepository;
        $this->warengruppeRepository = $warengruppeRepository;
        $this->userRepository = $userRepository;
        $this->artikelSortimentRepository = $artikelSortimentRepository;
    }

    public function handleGetRequest($url, Request $request, $id = null)
    {
        Log::info(['url' => $url, 'id' => $id]);
        switch ($url) {
            case 'artikel':
                return $this->artikelRepository->getAll();
            case 'categories': {
                    if ($id){
                        return $this->warengruppeRepository->getByCode($id);
                    }
                    else {
                        return $this->warengruppeRepository->getAll();
                    }
                }
            case 'kunden':
                return $this->userRepository->getAll();
            case 'users':
                    return $this->userRepository->getAll();

                {
                    if ($id){
                        return $this->warengruppeRepository->getByCode($id);
                    }
                    else {
                        return $this->warengruppeRepository->getAll();
                    }
                }
            default:
                return ['error' => 'Unbekannte API-Ressource'];
        }
    }

    public function handlePostRequest($url, Request $request, $id = null)
    {
        switch ($url) {
            case 'products': {
                if ( $this->artikelRepository->create($request->all()) ) {
                    $this->artikelSortimentRepository->create($request->all());
                    return true ;
                }
            }

            case 'categories':
                return $this->warengruppeRepository->create($request->all());
            case 'users': {
                  $id = $this->userRepository->create($request->all());
                  if ($id != false){
                    $response = [
                        'VERSION' => '1.7',
                        'request' => [ 'status' => 'success'],
                        'response' => [ 'result' => $id,
                                        'errors' => [] ] ];


                    return response()->json($response, 200);
                  }
                  else {
                    $response = [
                        'VERSION' => '1.7',
                        'request' => [ 'status' => 'error'],
                        'response' => [ 'result' => '',
                        'errors' => ['Fehler bei der Benutzeranlage'] ] ];

                    return response()->json($response, 401);

                  }
                }
            default:

                return ['error' => 'Unbekannte API-Ressource'];
        }
    }

    public function handlePatchRequest($url, Request $request, $id = null)
    {
        switch ($url) {
            case 'products': {
                $result = $this->artikelRepository->update($request->id, $request->all());

                $response = [
                    'Version' => 1.7,
                    'request' => [
                          'status' => ($result === true) ? 'warning' : 'error'
                        ],
                  'response' => [
                      'result' => null,
                      'errors' => [[]]
                    ]
                  ];
            }
            case 'categories':
                return $this->warengruppeRepository->update($request->id, $request->all());
            case 'users':
                return $this->userRepository->update($request->id, $request->all());
            default:
                return ['error' => 'Unbekannte API-Ressource'];
        }
    }

    public function handleDeleteRequest($url, Request $request, $id = null)
    {
        switch ($url) {
            case 'products': {
                    if ( $this->artikelRepository->delete($request->id) ){
                        return response('Artikel wurde erfolgreich geloescht', 200);
                    }
                    else {
                        return response('Artikel konnte nicht geloescht werden.', 500);
                    }
                }
            case 'categories':
                return $this->warengruppeRepository->delete($request->id);
            case 'kunden':
                return $this->userRepository->delete($request->id);
            default:
                return ['error' => 'Unbekannte API-Ressource'];
        }
    }
}
