<?php

namespace Tests\Service;

use App\Dao\Leilao as LeilaoDao;
use App\Model\Leilao;
use App\Service\Encerrador;
use App\Service\EnviadorEmail;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{

    private $encerrador;
    private $enviadorDeEmailMock;
    private $leilaoFiat;
    private $leilaoVariante;

    protected function setUp(): void
    {
        $this->leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$this->leilaoFiat, $this->leilaoVariante]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$this->leilaoFiat],
                [$this->leilaoVariante]
            );
        $this->enviadorDeEmailMock = $this->createMock(EnviadorEmail::class);
        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorDeEmailMock);
    }
    

    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $this->encerrador->encerra();
        $leiloesEncerrados = [$this->leilaoFiat, $this->leilaoVariante];
        static::assertCount(2, $leiloesEncerrados);
        static::assertTrue($leiloesEncerrados[0]->estaFinalizado());
        static::assertTrue($leiloesEncerrados[1]->estaFinalizado());
    }

    public function testDeveContinuarOProcessoamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException('Erro ao enviar e-mail');
        $this->enviadorDeEmailMock->expects(self::exactly(2))
            ->method('notificaTerminoLeilao')
            ->willThrowException($e);

        $this->encerrador->encerra();
    }
}
