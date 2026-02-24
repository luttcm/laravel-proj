<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNdsRequest;
use App\Http\Requests\UpdateNdsRequest;
use App\Repositories\NdsRepository;
use App\Services\NdsService;
use Illuminate\Http\Request;
use Exception;

class NdsController extends Controller
{
    protected $ndsRepository;
    protected $ndsService;

    public function __construct(NdsRepository $ndsRepository, NdsService $ndsService)
    {
        $this->ndsRepository = $ndsRepository;
        $this->ndsService = $ndsService;
    }

    public function index()
    {
        $nds = $this->ndsRepository->getAllPaginated(10);
        return view("pages.nds.index", compact("nds"));
    }

     public function add()
    {
        return view("pages.nds.add");
    }

    public function store(StoreNdsRequest $request) 
    {
        try {
            $this->ndsService->createNds($request->validated());
            return redirect()->route('nds.index')->with('success', "НДС создан!");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $nds = $this->ndsRepository->findById($id);
        return view('pages.nds.edit', compact('nds'));
    }

    public function update(UpdateNdsRequest $request, $id)
    {
        try {
            $this->ndsService->updateNds($id, $request->validated());
            return redirect()->route('nds.index')->with('success', 'НДС обновлен');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->ndsService->deleteNds($id);
        return redirect()->route('nds.index')->with('success', 'НДС удален');
    }
}
