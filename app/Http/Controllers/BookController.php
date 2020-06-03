<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class BookController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $books = Book::all();
    return new BookCollection($books);

    // $books = QueryBuilder::for(Book::class)->allowedSorts([
    //     'title',
    //     'publication_year',
    //     'created_at',
    //     'updated_at',
    // ])->jsonPaginate();
    // return new BooksCollection($books);
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
   * @param  App\Http\Requests\CreateBookRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(CreateBookRequest $request) {
    $book = Book::create([
      'title' => $request->input('data.attributes.title'),
      'description' => $request->input('data.attributes.description'),
      'publication_year' => $request->input('data.attributes.publication_year'),
    ]);

    return (new BookResource($book))
      ->response()
      ->header('Location', route('books.show', [
        'book' => $book,
      ]));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Book  $book
   * @return \Illuminate\Http\Response
   */
  public function show(Book $book) {
    return new BookResource($book);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Book  $book
   * @return \Illuminate\Http\Response
   */
  public function edit(Book $book) {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Requests\UpdateBookRequest  $request
   * @param  \App\Book  $book
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateBookRequest $request, Book $book) {
    $book->update($request->input('data.attributes'));

    return new BookResource($book);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Book  $book
   * @return \Illuminate\Http\Response
   */
  public function destroy(Book $book) {
    $book->delete();

    return response(null, 204);
  }
}
