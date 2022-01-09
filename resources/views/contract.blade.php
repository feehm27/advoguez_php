<!DOCTYPE html>
<html>
    <style>
        p {
            font-size: 20px;
            text-align: justify;
        }

        hr {
            margin-left: 0px;
            width:50%;
        }

    </style>
    <body>
        @php
            $client = $contract->client;
            $advocate = $contract->advocate;
        @endphp
        <div class="header">
            <h2>CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE ASSESSORIA JUDÍDICA CONTINUADA</h2>
        </div>
        <div class="body">
            <p>
                De um lado <b>{{$client['name']}}</b>, {{$client['nationality']}}, {{$client['civil_status']}}, inscrito no CPF sob nº {{$client['cpf']}}, 
                portador do RG nº {{$client['rg']}} - {{$client['issuing_organ']}}, residente e domiciliado no endereço:
                {{$client['street']}}, {{$client['number']}}, {{$client['district']}} – CEP: {{$client['cep']}},  
                {{$client['city']}}/{{$client['state']}} , doravante denominado <b>CONTRATANTE</b>.
            </p>
            <p>
                De outro, <b>{{$advocate['name']}}</b>, {{$advocate['nationality']}}, {{$advocate['civil_status']}},
                inscrito (a) na OAB/UF sob nº {{$advocate['register_oab']}}, com endereço profissional na 
                {{$advocate['street']}}, {{$advocate['number']}}, {{$advocate['district']}} – CEP: {{$advocate['cep']}},  
                {{$advocate['city']}}/{{$advocate['state']}}, aqui denominado <b>CONTRATADA.</b>
            </p>

            <h3>I - DO OBJETO</h3>
            <p>
                Cláusula 1ª. O objetivo do presente contrato é dar suporte jurídico à CONTRATANTE, atendendo suas necessidades legais, 
                cabendo à CONTRATADA a prestação de serviços de Consultoria e Assessoria Jurídica em esfera extrajudicial e judicial, 
                dentro do território nacional, com vigência imediata, e ainda, os serviços de Advocacia, em que a CONTRATANTE figure no
                polo passivo ou ativo, proporcionando atendimento jurídico em todas as instâncias das áreas cível, trabalhista, comercial 
                entre outras.
            </p>
            <p>
                Cláusula 2ª. A cobertura do presente serviço, acertado neste instrumento, consistirá em: prestar consultoria e assessoria 
                jurídica à CONTRATANTE, em suas atividades profissionais, dando todo suporte necessário para atender suas necessidades legais 
                em defesa de seus direitos e interesses junto a seus clientes, contratantes, imprensa e demais que se fizerem necessárias, 
                assim como, orientações jurídicas, elaboração de contratos, licenças, pareceres, notificações extrajudiciais, cobranças, 
                mediações, conciliações e lides judiciais.
            </p>
            <p>
                Parágrafo primeiro. A CONTRATADA se dispõe a efetuar viagens por todo o território nacional para realização dos atos previstos 
                nesse instrumento, quando se fizerem necessárias.
            </p>
            <p>
                Parágrafo segundo. No caso da CONTRATADA necessitar afastar-se por algum período desta Comarca, ou mesmo necessitar ser representada 
                em outra cidade, a CONTRATANTE autoriza, desde já, o substabelecimento dos poderes, com reservas, conferidos pela devida procuração, ficando, entretanto, sob a responsabilidade, única e exclusiva da CONTRATADA remuneração destes profissionais.
            </p>

            <h3>II - DOS HONORÁRIOS</h3>
            <p>
                Cláusula 3ª. Fica estabelecido que os honorários para a Prestação de Serviços de Assessoria Jurídica Continuada, previstos 
                nesse instrumento, será o equivalente à <b><u>R$ {{$contract['contract_price']}},00 mensais</u></b> sendo que a primeira parcela deverá ser paga, 
                com valor pro rata, no ato da assinatura deste instrumento e as demais deverão ser efetuadas consecutivamente sempre até o 
                dia <b><u>{{$contract['payment_day']}}</u></b>  do mês seguinte, com o valor integral acordado,
                 através de depósito bancário em conta corrente n.º <b><u>{{$contract['account']}}</u></b>, 
                da agência n.º <b><u>{{$contract['agency']}}</u></b>, do Banco <b><u>{{$contract['bank']}}</u></b>, de titularidade da CONTRATADA, ou em dinheiro, diretamente à CONTRATADA, que emitirá recibo.
                Parágrafo primeiro. Fica ainda pactuado, que além dos honorários mensais acima estabelecidos, a CONTRATADA fará jus a honorários 
                complementares, caso seja necessário ajuizar ações perante o Poder Judiciário, em todas as instâncias dos Tribunais. Nesse caso, 
                deverão ser firmados entre as partes, contratos adicionais, conforme análise do caso concreto, que serão anexados a esse;
            </p>
            <p>
                Parágrafo primeiro. Fica ainda pactuado, que além dos honorários mensais acima estabelecidos, a CONTRATADA fará jus a 
                honorários complementares, caso seja necessário ajuizar ações perante o Poder Judiciário, em todas as instâncias dos Tribunais.
                 Nesse caso, deverão ser firmados entre as partes, contratos adicionais, conforme análise do caso concreto, que serão anexados 
                a esse;
            </p>
            <p>
                Parágrafo segundo. Sempre que houver falta de pagamento dos honorários dentro dos prazos pactuados, sejam integrais ou parcelados, 
                fica acordada a aplicação de multa, a partir da data em que deveriam ter sido pagos, de 2% (dois por cento), para os pagamentos 
                em atraso, sendo ainda os valores atualizados pela variação verificada no período através do IGPM e cobrados juros de mora de 1% 
                ao mês;
            </p>

            <h3>III - DO PRAZO</h3>
            <p>
                Cláusula 4ª. O presente contrato terá duração de <b><u>{{$contract['contract_days']}} meses</u></b>, podendo ser prorrogado automaticamente, desde que não seja renunciado
                expressamente dentro do prazo de 30 (trinta dias) antes do término do mesmo, ou de sua prorrogação.
            </p>
            <p>
                Parágrafo único: Caso esteja em andamento alguma ação judicial, ou outro serviço extrajudicial, a rescisão deste não interfere, 
                nem cancela outro, salvo acordo expresso.
            </p>

            <h3>IV - DA RESCISÃO</h3>
            <p>
                Cláusula 5ª. O presente contrato poderá ser rescindido por livre acordo entre as partes, ou no caso de uma das partes 
                não cumprir com o estabelecido em qualquer das cláusulas deste instrumento, responsabilizando-se a que deu causa a pagar a multa 
                de  <b><u>R$ {{$contract['fine_price']}},00</u></b>.
            </p>

            <h3>V - CONSIDERAÇÕES GERAIS</h3>
            <p>Cláusula 6ª. O presente contrato passa a valer a partir da assinatura pelas partes.</p>
            <p>
                Cláusula 7ª. Fica acertado entre as partes que as informações prestadas entre as mesmas serão consideradas confidenciais e
                deverão ser mantidas em absoluto sigilo por ambas. Sobretudo no que tange aos trabalhos técnico-jurídicos desenvolvidos pela
                CONTRATADA A CONTRATANTE deverá reservar sigilo perante terceiros, inclusive do teor do presente contrato. 
                A obrigação de confidencialidade disposta nesta cláusula perdurará mesmo após o término, rescisão ou extinção do presente
                contrato;
            </p>
            <p>
                Cláusula 8ª. A CONTRATADA poderá prestar serviços a outros contratantes durante a vigência desse contrato,
                exceto aos concorrentes profissionais da CONTRATANTE.
            </p>

            <h3>VI - DO FORO</h3>
            <p>
                Cláusula 9ª. Para dirimir quaisquer controvérsias oriundas do CONTRATO, as partes elegem o foro da
                comarca de <u>Belo Horizonte</u>.
            </p>
            <p>
                E por estarem justas e acertadas, assinam o presente 
                em 02 (duas) vias de igual teor e forma, na presença de 02 (duas) testemunhas instrumentárias.
            </p>

            <div class="signature">
                <p>{{$advocate['city']}}, {{$contract['day']}} de {{$contract['month']}} de {{$contract['year']}}.</p>

                <br><br>
                    <hr>
                    <p>CONTRATANTE</p>

                <br><br>
                    <hr>
                    <p>CONTRATADA</p>
                
                <br><br>
                    <hr>
                    <p>TESTEMUNHA</p>
                    <p>CPF:</p>
            </div>     
        </div>
    </body>
</html>