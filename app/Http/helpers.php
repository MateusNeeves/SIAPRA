<?php

function normalizarQuarentena($valor) {
    return normalizarTextoEnum($valor);
}

function normalizarTextoEnum($valor) {
    if (!is_string($valor)) {
        return $valor;
    }

    $brancos = preg_quote(espacosTextoEnum(), '/');
    $valor = preg_replace('/^[' . $brancos . ']+|[' . $brancos . ']+$/u', '', $valor) ?? $valor;
    // A transliteração também pode produzir espaços: aparar depois preserva os legados.
    return strtoupper(trim(\Illuminate\Support\Str::ascii($valor)));
}

function espacosTextoEnum() {
    // Unicode White_Space e BOM nas bordas; compartilhados com TRIM no SQL.
    return " \t\n\r\x0B\x0C\u{0085}\u{00A0}\u{1680}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200A}\u{2028}\u{2029}\u{202F}\u{205F}\u{3000}\u{FEFF}";
}

function quarentenaSql() {
    // Normaliza os valores legados sem depender da collation do banco.
    return statusLoteSql('L.QUARENTENA');
}

function normalizarStatusLote($valor) {
    return normalizarTextoEnum($valor);
}

function statusLoteExigeDescricao($valor) {
    return in_array(normalizarStatusLote($valor), ['REPROVADO', 'DESCARTADO'], true);
}

// A coluna deve ser uma referência SQL interna, nunca entrada do usuário.
function normalizarTextoEnumSql($coluna, $acentos) {
    $brancos = espacosTextoEnum();
    $sql = "TRIM($coluna, '$brancos')";
    foreach ($acentos as $origem => $destino) {
        $sql = "REPLACE($sql, '$origem', '$destino')";
    }
    return "UPPER(TRIM($sql, '$brancos'))";
}

function statusLoteSql($coluna = 'S.STATUS') {
    $acentos = [];
    foreach (['a' => 'áàâãäÁÀÂÃÄ', 'e' => 'éèêëÉÈÊË', 'i' => 'íìîïÍÌÎÏ',
        'o' => 'óòôõöÓÒÔÕÖ', 'u' => 'úùûüÚÙÛÜ', 'c' => 'çÇ'] as $ascii => $letras) {
        foreach (mb_str_split($letras) as $letra) {
            $acentos[$letra] = $ascii;
        }
    }
    // Formas decompostas dos mesmos acentos (sem extensão unaccent).
    foreach (["\u{0300}", "\u{0301}", "\u{0302}", "\u{0303}", "\u{0308}", "\u{0327}"] as $marca) {
        $acentos[$marca] = '';
    }
    return normalizarTextoEnumSql($coluna, $acentos);
}

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
