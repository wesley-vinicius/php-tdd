<?php

namespace App\Service;

use App\Model\Leilao;

class EnviadorEmail
{
    public function notificaTerminoLeilao(Leilao $leilao)
    {
        $sucesso = mail(
            'usuario@example.com',
            'Leilão finalizado',
            "Leilão para {$leilao->recuperarDescricao()} finalizado."
        );

        if (!$sucesso) {
            throw new \DomainException('Erro ao enviar e-mail');
        }
    }
}