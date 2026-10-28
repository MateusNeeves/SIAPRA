<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuantidadeTest extends TestCase
{
    public static function valores(): array
    {
        return [
            ['12.000', '12', '12'], ['50.000', '50', '50'],
            ['1.500', '1.5', '1,5'], ['2.750', '2.75', '2,75'],
            ['0.125', '0.125', '0,125'], ['0.000', '0', '0'],
            ['-2.750', '-2.75', '-2,75'], ['-12.000', '-12', '-12'],
            ['100', '100', '100'], ['1000.000', '1000', '1000'],
            ['9999999.999', '9999999.999', '9999999,999'],
            ['12345678901234567890.123456789000', '12345678901234567890.123456789', '12345678901234567890,123456789'],
            [null, '', ''], ['', '', ''], ['abc.000', 'abc.000', 'abc.000'],
            ['1.2.000', '1.2.000', '1.2.000'], ['1,500', '1,500', '1,500'],
            [' 12.000 ', ' 12.000 ', ' 12.000 '],
        ];
    }

    #[DataProvider('valores')]
    public function test_formata_sem_arredondar_ou_mascarar_invalidos($valor, string $normalizado, string $formatado): void
    {
        $this->assertSame($normalizado, normalizarQuantidade($valor));
        $this->assertSame($formatado, formatarQuantidade($valor));
    }
}
