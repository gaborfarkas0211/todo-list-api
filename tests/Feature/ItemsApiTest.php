<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Str;
use Tests\TestCase;

class ItemsApiTest extends TestCase
{
    use DatabaseTransactions;

    private const API_URL = '/api/items';
    private const SUCCESS_ITEM_STRUCTURE = [
        'success',
        'data' => self::ITEM_STRUCTURE
    ];
    private const ITEM_STRUCTURE = [
        'id',
        'description',
        'completed',
        'created_at',
        'updated_at',
    ];
    private const CREATE_OR_UPDATE_PAYLOADS = [
        ['name' => 'Buy a milk'],
        ['name' => 'Clean the room', 'description' => 'Clean the room with your brother'],
    ];

    public function setUp(): void
    {
        parent::setUp();
        Item::factory()->count(100)->create();
    }

    public function testGetAllItems(): void
    {
        $this->get(self::API_URL)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => self::ITEM_STRUCTURE
                    ],
                    'links' => ['first', 'last', 'prev', 'next'],
                    'meta' => [
                        'current_page',
                        'from',
                        'last_page',
                        'path',
                        'per_page',
                        'to',
                        'total',
                        'links' => [
                            '*' => [
                                'url',
                                'label',
                                'active',
                            ],
                        ],
                    ],
                ]
            ]);
    }

    public function testGetItem(): void
    {
        $this->testingWithMultipleItems(
            fn(Item $item) => $this->assertSuccessResponse($this->get('/api/items/' . $item->id))
        );
    }

    public function testCreateItems(): void
    {
        foreach (self::CREATE_OR_UPDATE_PAYLOADS as $payload) {
            $response = $this->post(self::API_URL, $payload);
            $data = $payload;
            if (!\Arr::has($payload, 'description')) {
                data_set($data, 'description', null);
            }

            $this->assertSuccessResponse($response, $data);
        }
    }

    public function testUpdateItems()
    {
        $index = 0;
        $this->testingWithMultipleItems(function (Item $item) use (&$index) {
            $payload = self::CREATE_OR_UPDATE_PAYLOADS[$index];
            $response = $this->put($this->getApiUrl($item), $payload);
            $data = $payload + $item->only(['id', 'name', 'description']);

            $this->assertSuccessResponse($response, $data);
            $index++;
        }, 2);
    }

    public function testDeleteItems()
    {
        $this->testingWithMultipleItems(function (Item $item) {
            $this->delete($this->getApiUrl($item))
                ->assertJsonStructure(['success', 'data']);
            $this->assertDatabaseMissing('lists', ['id' => $item->id]);
        });
    }

    public function testItemsValidation()
    {
        $payloads = [
            [
                'data' => ['name' => null],
                'errors' => ['name' => ['The name field is required.']],
            ],
            [
                'data' => ['name' => Str::random(81), 'description' => Str::random(751), 'completed' => 2],
                'errors' => [
                    'name' => ['The name must not be greater than 80 characters.'],
                    'description' => ['The description must not be greater than 750 characters.'],
                    'completed' => ['The completed field must be true or false.'],
                ],
            ],
        ];

        $itemForUpdate = Item::first();
        foreach ($payloads as $payload) {
            $data = $payload;
            unset($payload['errors']['completed']);
            $this->post(self::API_URL, $payload['data'])
                ->assertJson([
                    'success' => false,
                    'data' => $payload['errors'],
                ]);
            $payload = $data;
            $this->put(self::getApiUrl($itemForUpdate), $payload['data'])
                ->assertJson([
                    'success' => false,
                    'data' => $payload['errors'],
                ]);
        }
    }

    public function testPurifiedItems()
    {
        $data = [
            ["<script>alert('a')</script>Test", 'Test'],
            ["<body>Test</body>", 'Test'],
            ['<div>Test</div>', false],
            ['<b>Test</b>', false],
            ['<strong>Test</strong>', false],
            ['<i>Test</i>', false],
            ['<em>Test</em>', false],
            ['<u>Test</u>', false],
            ['<a>Test</a>', false],
            ['<ul>Test</ul>', false],
            ['<ol>Test</ol>', false],
            ['<li>Test</li>', false],
            ['<p>Test</p>', false],
            ['Test<br>Test', false],
            ['<span>Test</span>', false],
        ];

        foreach ($data as [$value, $newValue]) {
            $response = $this->post(self::API_URL, ['name' => $value]);
            $this->assertSuccessResponse($response);
            $this->assertDatabaseHas('lists', [
                'name' => $newValue ?? $value,
                'description' => null,
            ]);
        }
    }

    private function testingWithMultipleItems(callable $callback, $count = 5)
    {
        $items = Item::limit($count)->get();
        foreach ($items as $item) {
            $callback($item);
        }
    }

    private static function getApiUrl(Item $item = null): string
    {
        return self::API_URL . '/' . $item->id;
    }

    public function assertSuccessResponse(TestResponse $response, array $data = []): void
    {
        $response->assertStatus(200)
            ->assertJsonStructure(self::SUCCESS_ITEM_STRUCTURE);
        $this->assertDatabaseHas('lists', $data);
    }
}
