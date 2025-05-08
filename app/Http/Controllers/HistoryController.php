<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetHistoryRequest;
use App\Http\Resources\HistoryResource;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Throwable;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws Throwable
     */
    public function index(GetHistoryRequest $request): ResourceCollection
    {
        $filter = $request->safe()->only('filter');
        $pagination = $request->safe()->only('pagination');

        $result = [];
        if (!empty($filter) && !empty($pagination)) {
            $filter = $filter['filter'];
            $pagination = $pagination['pagination'];
            $result = History::filterHistory($filter)->with('stock')->simplePaginate(perPage: $pagination['perPage'], page: $pagination['page']);
        } elseif (!empty($filter)) {
            $filter = $filter['filter'];
            $result = History::filterHistory($filter)->with('stock')->get();
        } elseif (!empty($pagination)) {
            $pagination = $pagination['pagination'];
            $result = History::query()->with('stock')->simplePaginate(perPage: $pagination['perPage'], page: $pagination['page']);
        } else {
            $result = History::query()->with('stock')->get();
        }

        return HistoryResource::collection($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(History $history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, History $history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(History $history)
    {
        //
    }
}
