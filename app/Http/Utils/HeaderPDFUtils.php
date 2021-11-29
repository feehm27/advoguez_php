<?php

namespace App\Http\Utils;

class HeaderPDFUtils
{
    const HEADER_CLIENTS = ['Nome','Email','CPF','RG','Nacionalidade','Data de nascimento','Gênero',
    'Estado Civil','Telefone Fixo','Telefone Celular'];

    const ATTRIBUTES_CLIENT = ['name', 'email', 'cpf', 'rg', 'nationality', 'birthday', 'gender', 
    'civil_status','telephone', 'cellphone'];
}