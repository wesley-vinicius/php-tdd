<?php

namespace App\Service;

use App\Dao\Leilao as LeilaoDao;

class Encerrador
{
    public function encerra()
    {
        $dao = new LeilaoDao();
        $leiloes = $dao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                $leilao->finaliza();
                $dao->atualiza($leilao);
            }
        }
    }
}
