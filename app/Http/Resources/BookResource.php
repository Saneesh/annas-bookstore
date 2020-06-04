<?php

namespace App\Http\Resources;

use App\Http\Resources\AuthorsIdentifierResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource {
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request) {
    return [
      'id' => (string) $this->id,
      'type' => 'books',
      'attributes' => [
        'title' => $this->title,
        'description' => $this->description,
        'publication_year' => $this->publication_year,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ],
      'relationships' => [
        'authors' => [
          'links' => [
            'self' => route('books.relationships.authors', [
              'book' => $this->id,
            ]),
            'related' => route('books.authors', [
              'book' => $this->id,
            ]),
          ],
          'data' => AuthorsIdentifierResource::collection($this->authors),
        ],
      ],
    ];
  }
}
