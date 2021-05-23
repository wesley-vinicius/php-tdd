<?php

namespace Tests\Integration\Dao;

use App\Dao\Leilao as DaoLeilao;
use App\Infra\ConnectionCreator;
use App\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{

    private static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes (
            id INTEGER primary key,
            descricao TEXT,
            finalizado BOOL,
            dataInicio TEXT
        );');
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    public function testInsercaoEBuscaDevemFuncionar()
    {
        $leilao = new Leilao('Variante 0Km');
        $leilaoDao = new DaoLeilao(self::$pdo);
        $leilaoDao->salva($leilao);

        $leiloes = $leilaoDao->recuperarNaoFinalizados();
    
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame(
            'Variante 0Km',
            $leiloes[0]->recuperarDescricao()
        );
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}
