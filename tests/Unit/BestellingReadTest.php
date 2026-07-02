<?php

namespace Tests\Unit;

use App\Http\Controllers\BestellingController;
use App\Models\Bestelling;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class BestellingReadTest extends TestCase
{
    public function test_index_geeft_producten_view_terug_met_data(): void
    {
        $bestellingen = [
            (object) [
                'Id' => 1,
                'ProductNaam' => 'Shampoo',
                'KlantNaam' => 'Jan Jansen',
                'Orderdatum' => '2026-07-01',
                'VerwachteLeverdatum' => '2026-07-04',
                'Status' => 'In behandeling',
            ],
        ];

        $bestellingMock = Mockery::mock(Bestelling::class);
        $bestellingMock->shouldReceive('getAllBestellingen')
            ->once()
            ->andReturn($bestellingen);

        $controller = new BestellingController($bestellingMock);
        $response = $controller->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('bestellingen.index', $response->name());
        $this->assertSame($bestellingen, $response->getData()['bestellingen']);
    }

    public function test_index_geeft_lege_lijst_bij_fout_in_read_call(): void
    {
        $bestellingMock = Mockery::mock(Bestelling::class);
        $bestellingMock->shouldReceive('getAllBestellingen')
            ->once()
            ->andThrow(new \Exception('Database fout'));

        $controller = new BestellingController($bestellingMock);
        $response = $controller->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('bestellingen.index', $response->name());
        $this->assertSame([], $response->getData()['bestellingen']);
    }
}