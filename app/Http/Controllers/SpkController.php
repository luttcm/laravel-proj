<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpkRequest;
use App\Http\Requests\UpdateSpkRequest;
use App\Repositories\SpkRepository;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    protected $spkRepository;

    public function __construct(SpkRepository $spkRepository)
    {
        $this->spkRepository = $spkRepository;
    }

    public function index()
    {
        $spks = $this->spkRepository->getAllPaginated(10);
        return view("pages.spk.index", compact("spks"));
    }

    public function add()
    {
        return view("pages.spk.add");
    }

    public function store(StoreSpkRequest $request) 
    {
        $this->spkRepository->create($request->validated());

        return redirect()->route('spk.index')
            ->with('success', "СПК создан!");
    }

    public function edit($id)
    {
        $spk = $this->spkRepository->findById($id);
        return view('pages.spk.edit', compact('spk'));
    }

    public function update(UpdateSpkRequest $request, $id)
    {
        $this->spkRepository->update($id, $request->validated());

        return redirect()->route('spk.index')->with('success', 'СПК обновлен');
    }

    public function delete($id)
    {
        $this->spkRepository->delete($id);
        return redirect()->route('spk.index')->with('success', 'СПК удален');
    }
}
