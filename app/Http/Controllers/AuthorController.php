<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $authors = QueryBuilder::for(Author::class)
        ->allowedSorts([
            'name'
        ])->jsonPaginate();

    //return new AuthorCollection($authors);
    return AuthorResource::collection($authors);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create() {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Requests\CreateAuthorRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CreateAuthorRequest $request) {
    $author = Author::create([
      'name' => $request->input('data.attributes.name'),
    ]);

    return (new AuthorResource($author))
      ->response()
      ->header('Location', route('authors.show', [
        'author' => $author,
      ]));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Author  $author
   * @return \Illuminate\Http\Response
   */
  public function show(Author $author) {
    return new AuthorResource($author);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Author  $author
   * @return \Illuminate\Http\Response
   */
  public function edit(Author $author) {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Requests\UpdateAuthorRequest  $request
   * @param  \App\Author  $author
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateAuthorRequest $request, Author $author) {
    $author->update($request->input('data.attributes'));

    return new AuthorResource($author);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Author  $author
   * @return \Illuminate\Http\Response
   */
  public function destroy(Author $author) {
    $author->delete();

    return response(null, 204);
  }
}
