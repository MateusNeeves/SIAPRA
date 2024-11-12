<?php

function formatCpf($cpf) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
}

function formatCnpj($cnpj) {
  return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj);
}

function formatPhone($phone) {
    $pattern = strlen($phone) === 10 ? '(\d{2})(\d{4})(\d{4})' : '(\d{2})(\d{5})(\d{4})';
    return preg_replace("/$pattern/", "($1) $2-$3", $phone);
}

function formatCep($cep) {
  return preg_replace("/(\d{5})(\d{3})/", "$1-$2", $cep);
}