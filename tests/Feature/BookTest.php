<?php
namespace Tests\Feature;
use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BookTest extends TestCase {
  use DatabaseMigrations;

  /**
   * @test
   */
  public function it_returns_a_book_as_a_resource_object() {
    $book = factory(Book::class)->create();
    $user = factory(User::class)->create();

    Passport::actingAs($user);

    $this->getJson('/api/v1/books/1', [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(200)
      ->assertJson([
        "data" => [
          "id" => '1',
          "type" => "books",
          "attributes" => [
            'title' => $book->title,
            'description' => $book->description,
            'publication_year' => $book->publication_year,
            'created_at' => $book->created_at->toJSON(),
            'updated_at' => $book->updated_at->toJSON(),
          ],
        ],
      ]);
  }

  /**
   * @test
   */
  public function it_returns_all_books_as_a_collection_of_resource_objects() {
    $books = factory(Book::class, 2)->create();
    $user = factory(User::class)->create();

    Passport::actingAs($user);

    $this->getJson('/api/v1/books', [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => '1',
            "type" => "books",
            "attributes" => [
              'title' => $books[0]->title,
              'description' => $books[0]->description,
              'publication_year' => $books[0]->publication_year,
              'created_at' => $books[0]->created_at->toJSON(),
              'updated_at' => $books[0]->updated_at->toJSON(),
            ],
          ],
          [
            "id" => '2',
            "type" => "books",
            "attributes" => [
              'title' => $books[1]->title,
              'description' => $books[1]->description,
              'publication_year' => $books[1]->publication_year,
              'created_at' => $books[1]->created_at->toJSON(),
              'updated_at' => $books[1]->updated_at->toJSON(),
            ],
          ],
        ],
      ]);
  }

  /**
   * @test
   */
  public function it_can_create_a_book_from_a_resource_object() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $attributes = [
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
      'created_at' => now()->setMilliseconds(0)->toJSON(),
      'updated_at' => now()->setMilliseconds(0)->toJSON(),
    ];

    $res = $this->postJson('/api/v1/books', [
      'data' => [
        'type' => 'books',
        'attributes' => $attributes,
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(201)
      ->assertJson([
        "data" => [
          "id" => '1',
          "type" => "books",
          "attributes" => $attributes,
        ],
      ])
      ->assertHeader('Location', url('/api/v1/books/1'));

    $this->assertDatabaseHas('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_the_type_member_is_given_when_creating_a_book() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $attributes = [
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
      'created_at' => now()->setMilliseconds(0)->toJSON(),
      'updated_at' => now()->setMilliseconds(0)->toJSON(),
    ];

    $res = $this->postJson('/api/v1/books', [
      'data' => [
        'type' => '',
        'attributes' => $attributes,
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The data.type field is required.',
            'source' => [
              'pointer' => '/data/type',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_the_type_member_has_the_value_of_books_when_creating_a_book() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $attributes = [
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
      'created_at' => now()->setMilliseconds(0)->toJSON(),
      'updated_at' => now()->setMilliseconds(0)->toJSON(),
    ];

    $res = $this->postJson('/api/v1/books', [
      'data' => [
        'type' => 'boo',
        'attributes' => $attributes,
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The selected data.type is invalid.',
            'source' => [
              'pointer' => '/data/type',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_the_attributes_member_has_been_given_when_creating_a_book() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/books', [
      'data' => [
        'type' => 'books',
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The data.attributes field is required.',
            'source' => [
              'pointer' => '/data/attributes',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_the_attributes_member_is_an_array_given_when_creating_a_book() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $this->postJson('/api/v1/books', [
      'data' => [
        'type' => 'books',
        'attributes' => 'This is not an object',
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The data.attributes must be an array.',
            'source' => [
              'pointer' => '/data/attributes',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_a_title_attribute_is_given_when_creating_a_book() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $attributes = [
      'description' => 'A book about API development',
      'publication_year' => '2019',
      'created_at' => now()->setMilliseconds(0)->toJSON(),
      'updated_at' => now()->setMilliseconds(0)->toJSON(),
    ];

    $res = $this->postJson('/api/v1/books', [
      'data' => [
        'type' => 'books',
        'attributes' => $attributes,
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The data.attributes.title field is required.',
            'source' => [
              'pointer' => '/data/attributes/title',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_a_title_attribute_is_a_string_when_creating_a_book() {
    $user = factory(User::class)->create();
    Passport::actingAs($user);

    $attributes = [
      'title' => 100,
      'description' => 'A book about API development',
      'publication_year' => '2019',
      'created_at' => \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.000000\Z'),
      'updated_at' => \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.000000\Z'),
    ];

    $res = $this->postJson('/api/v1/books', [
      'data' => [
        'type' => 'books',
        'attributes' => $attributes,
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The data.attributes.title must be a string.',
            'source' => [
              'pointer' => '/data/attributes/title',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @ test
   */
  public function it_validates_that_a_description_attribute_is_given_when_creating_an_book() {
  }

  /**
   * @ test
   */
  public function it_validates_that_a_description_attribute_is_a_string_when_creating_an_() {
  }

  /**
   * @ test
   */
  public function it_validates_that_a_publication_year_attribute_is_given_when_creating_a() {
  }

  /**
   * @ test
   */
  public function it_validates_that_a_publication_year_attribute_is_a_string_when_creatin() {
  }

  /**
   * @test
   */
  public function it_can_update_an_book_from_a_resource_object() {
    $user = factory(User::class)->create();

    Passport::actingAs($user);

    $book = factory(Book::class)->create();

    $this->patchJson('/api/v1/books/1', [
      'data' => [
        'id' => '1',
        'type' => 'books',
        'attributes' => [
          'title' => 'Building an API with Laravel',
          'description' => 'A book about API development',
          'publication_year' => '2019',
        ],
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(200)
      ->assertJson([
        "data" => [
          "id" => '1',
          "type" => "books",
          "attributes" => [
            'title' => 'Building an API with Laravel',
            'description' => 'A book about API development',
            'publication_year' => '2019',
            'created_at' => now()->setMilliseconds(0)->toJSON(),
            'updated_at' => now()->setMilliseconds(0)->toJSON(),
          ],
        ],
      ]);
    $this->assertDatabaseHas('books', [
      'id' => 1,
      'title' => 'Building an API with Laravel',
      'description' => 'A book about API development',
      'publication_year' => '2019',
    ]);
  }

  /**
   * @test
   */
  public function it_validates_that_an_id_member_is_given_when_updating_a_book() {
    $user = factory(User::class)->create();

    Passport::actingAs($user);

    $book = factory(Book::class)->create();

    $this->patchJson('/api/v1/books/1', [
      'data' => [
        'type' => 'books',
        'attributes' => [
          'title' => 'Building an API with Laravel',
          'description' => 'A book about API development',
          'publication_year' => '2019',
        ],
      ],
    ], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])
      ->assertStatus(422)
      ->assertJson([
        'errors' => [
          [
            'title' => 'Validation Error',
            'details' => 'The data.id field is required.',
            'source' => [
              'pointer' => '/data/id',
            ],
          ],
        ],
      ]);

    $this->assertDatabaseHas('books', [
      'id' => 1,
      'title' => $book->title,
    ]);
  }

  /**
   * @ test
   */
  public function it_validates_that_an_id_member_is_a_string_when_updating_an_book() {
  }

  /**
   * @ test
   */
  public function it_validates_that_the_type_member_is_given_when_updating_an_book() {
  }

  /**
   * @ test
   */
  public function it_validates_that_the_type_member_has_the_value_of_books_when_updating_an() {
  }

  /**
   * @ test
   */
  public function it_validates_that_the_attributes_member_has_been_given_when_updating_an() {
  }

  /**
   * @ test
   */
  public function it_validates_that_the_attributes_member_is_an_object_given_when_updatin() {
  }

  /**
   * @ test
   */
  public function it_validates_that_a_title_attribute_is_a_string_when_updating_an_book() {
  }

  /**
   * @ test
   */
  public function it_validates_that_a_description_attribute_is_a_string_when_updating_an_() {}

  /**
   * @ test
   */
  public function it_validates_that_a_publication_year_attribute_is_a_string_when_updating_() {
  }

  /**
   * @test
   */
  public function it_can_delete_an_book_through_a_delete_request() {
    $user = factory(User::class)->create();

    Passport::actingAs($user);

    $book = factory(Book::class)->create();

    $this->delete('/api/v1/books/1', [], [
      'accept' => 'application/vnd.api+json',
      'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    $this->assertDatabaseMissing('books', [
      'id' => 1,
      'title' => $book->title,
    ]);
  }

  /**
   * @ test
   */
  public function it_can_sort_books_by_title_through_a_sort_query_parameter() {
  }

  /**
   * @ test
   */
  public function it_can_sort_books_by_title_in_descending_order_through_a_sort_query_param() {
  }

  /**
   * @ test
   */
  public function it_can_sort_books_by_multiple_attributes_through_a_sort_query_parameter() {
  }

  /**
   * @ test
   */
  public function it_can_sort_books_by_multiple_attributes_in_descending_order_through_a_() {
  }

  /**
   * @ test
   */
  public function it_can_paginate_books_through_a_page_query_parameter() {
  }

  /**
   * @ test
   */
  public function it_can_paginate_books_through_a_page_query_parameter_and_show_different() {
  }
}