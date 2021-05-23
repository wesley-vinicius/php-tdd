<?php

namespace Tests\Service;

use App\Dao\Leilao as LeilaoDao;
use App\Model\Leilao;
use App\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$leilaoFiat, $leilaoVariante]);
        $leilaoDao->expects(self::once())
            ->method('recuperarNaoFinalizados')
            ->willReturn([$leilaoFiat, $leilaoVariante]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive([$leilaoFiat], [$leilaoVariante]);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        $leiloesEncerrados = [$leilaoFiat, $leilaoVariante];
        static::assertCount(2, $leiloesEncerrados);
        static::assertTrue($leiloesEncerrados[0]->estaFinalizado());
        static::assertTrue($leiloesEncerrados[1]->estaFinalizado());

        static::assertEquals(
            'Fiat 147 0Km',
            $leiloesEncerrados[0]->recuperarDescricao()
        );
        static::assertEquals(
            'Variante 0Km',
            $leiloesEncerrados[1]->recuperarDescricao()
        );
    }
}