<?php

namespace App\Http\Utils;

class HeaderPDFUtils
{
    const HEADER_CLIENTS = ['Nome','Email','CPF','RG','Nacionalidade','Data de nascimento','Gênero',
    'Estado Civil','Telefone Fixo','Telefone Celular'];

    const ATTRIBUTES_CLIENT = ['name', 'email', 'cpf', 'rg', 'nationality', 'birthday', 'gender', 
    'civil_status','telephone', 'cellphone'];

    const HEADER_CLIENTS_REPORT =  ['Nome','Email','CPF','RG','Data de nascimento','Gênero',
    'Estado Civil','Telefone Celular', 'Endereço', 'Data de cadastro'];

    const ATTRIBUTES_CLIENT_REPORT = ['name', 'email', 'cpf', 'rg', 'birthday', 'gender', 
    'civil_status','cellphone', 'street', 'district', 'state', 'city', 'number', 'created_at'];

    const HEADER_CONTRACTS_REPORT =  ['Nome do cliente', 'Data de inicio','Data de encerramento','Data de cancelamento', 'Dia do pagamento',
    'Valor do contrato','Valor da multa', 'Dados do pagamento'];

    const ATTRIBUTES_CONTRACT_REPORT = ['client_id', 'start_date', 'finish_date', 'canceled_at', 'payment_day', 'contract_price', 
    'fine_price','agency', 'account', 'bank'];

    const HEADER_PROCESSES_REPORT =  ['Nome do cliente', 'Número do processo', 'Data de inicio','Data de encerramento', 'Etapa', 'Vara Trabalhista',
    'Assunto'];

    const ATTRIBUTES_PROCESSES_REPORT = ['client_id', 'number', 'start_date', 'end_date', 'status', 'labor_stick', 'petition'];

}