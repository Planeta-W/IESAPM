<?php

// ============== FUNÇÃO CHECAR SE O CAMPO ESTÁ VAZIO (JSON) ===========
// =====================================================================

function checarVazio($campo, $nomeCampo){
	if(empty($campo)){

		$responseError = array(
      'status' => 'erro',
			'erro' => 'Preencha corretamente o campo "' . $nomeCampo . '"'
		);

	  echo json_encode($responseError);
		exit;
	}
}

// ======================= FUNÇÃO LIMPAR STRING ========================
// =====================================================================

function clean($string) {
	$string = str_replace(' ', '-', $string);
	$string = str_replace("-", "", $string);
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}

function removerCaracEspec($string) {
	$string = preg_replace('/[^a-zA-Z0-9_ -]/s',' ',$string);
	return str_replace('-', ' ', $string);
}

function removerNumeros($string) {
	return preg_replace('/[0-9]+/', '',$string);
}

// ================== FUNÇÃO CHECAR TAMANHO MÁXIMO =====================
// =====================================================================

function tamanho_max($string, $max_val) {

	if(strlen($string) > $max_val){
		return false;
	}else{
		return true;
	}

}

// ================== FUNÇÃO CHECAR TAMANHO MÍNIMO =====================
// =====================================================================

function tamanho_min($string, $min_val) {

	if(strlen($string) < $min_val){
		return false;
	}else{
		return true;
	}

}

// ======================= FUNÇÃO VERIFICAR CPF ========================
// =====================================================================

function validaCPF($cpf) {
  $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
  if (strlen($cpf) != 11) {
    return false;
  }

  if (preg_match('/(\d)\1{10}/', $cpf)) {
    return false;
  }

  for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
          $d += $cpf[$c] * (($t + 1) - $c);
      }
      $d = ((10 * $d) % 11) % 10;
      if ($cpf[$c] != $d) {
          return false;
      }
  }

  return true;
}

// ==================== FUNÇÃO VERIFICAR CARTÃO ========================
// =====================================================================

function validarCartao($number) {

  // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
  $number = preg_replace('/\D/', '', $number);

  // Set the string length and parity
  $number_length = strlen($number);
  $parity = $number_length % 2;

  // Loop through each digit and do the maths
  $total = 0;
  for ($i = 0; $i < $number_length; $i++) {
    $digit = $number[$i];

		// Multiply alternate digits by two
    if($i % 2 == $parity) {
      $digit *= 2;
      // If the sum is two digits, add them together (in effect)
      if ($digit > 9) {
        $digit -= 9;
      }
    }
    // Total up the digits
    $total += $digit;
  }

  // If the total mod 10 equals 0, the number is valid
  return ($total % 10 == 0) ? TRUE : FALSE;

}

// ======================= FUNÇÃO GERAR STRING =========================
// =====================================================================

function gerarString($length = 4) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// ======================= FUNÇÃO GERAR NUMERO =========================
// =====================================================================

function gerarNumero($length = 4) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// FUNÇÃO RETORNAR ESTADOS DO BRASIL
// =================================

function retorna_ufs(){
	return array(
		"AC","AL","AM","AP","BA","CE","DF",
		"ES","GO","MA","MG","MS","MT","PA",
		"PB","PE","PI","PR","RJ","RN","RO",
		"RR","RS","SC","SE","SP","TO"
	);
}

// FUNÇÃO RETORNAR PAÍSES DO MUNDO
// =================================

function retorna_paises(){
	return array(
		"África do Sul", "Albânia", "Alemanha", "Andorra", "Angola", "Anguilla", "Antigua", "Arábia Saudita", "Argentina", "Armênia", "Aruba",
		"Austrália", "Áustria", "Azerbaijão", "Bahamas", "Bahrein", "Bangladesh", "Barbados", "Bélgica", "Benin", "Bermudas", "Botsuana",
		"Brasil", "Brunei", "Bulgária", "Burkina Fasso", "botão", "Cabo Verde", "Camarões", "Camboja", "Canadá", "Cazaquistão", "Chade", "Chile",
		"China", "Cidade do Vaticano", "Colômbia", "Congo", "Coréia do Sul", "Costa do Marfim", "Costa Rica", "Croácia", "Dinamarca", "Djibuti", "Dominica",
		"EUA", "Egito", "El Salvador", "Emirados Árabes", "Equador", "Eritréia", "Escócia", "Eslováquia", "Eslovênia", "Espanha", "Estônia", "Etiópia",
		"Fiji", "Filipinas", "Finlândia", "França", "Gabão", "Gâmbia", "Gana", "Geórgia", "Gibraltar", "Granada", "Grécia", "Guadalupe", "Guam", "Guatemala",
		"Guiana", "Guiana Francesa", "Guiné-bissau", "Haiti", "Holanda", "Honduras", "Hong Kong", "Hungria", "Iêmen", "Ilhas Cayman", "Ilhas Cook", "Ilhas Curaçao",
		"Ilhas Marshall", "Ilhas Turks &amp; Caicos", "Ilhas Virgens (brit.)", "Ilhas Virgens(amer.)", "Ilhas Wallis e Futuna", "Índia", "Indonésia", "Inglaterra",
		"Irlanda", "Islândia", "Israel", "Itália", "Jamaica", "Japão", "Jordânia", "Kuwait", "Latvia", "Líbano", "Liechtenstein", "Lituânia", "Luxemburgo", "Macau",
		"Macedônia", "Madagascar", "Malásia", "Malaui", "Mali", "Malta", "Marrocos", "Martinica", "Mauritânia", "Mauritius", "México", "Moldova", "Mônaco",
		"Montserrat", "Nepal", "Nicarágua", "Niger", "Nigéria", "Noruega", "Nova Caledônia", "Nova Zelândia", "Omã", "Palau", "Panamá", "Papua-nova Guiné",
		"Paquistão", "Peru", "Polinésia Francesa", "Polônia", "Porto Rico", "Portugal", "Qatar", "Quênia", "Rep. Dominicana", "Rep. Tcheca", "Reunion", "Romênia",
		"Ruanda", "Rússia", "Saipan", "Samoa Americana", "Senegal", "Serra Leone", "Seychelles", "Singapura", "Síria", "Sri Lanka", "St. Kitts &amp; Nevis",
		"St. Lúcia", "St. Vincent", "Sudão", "Suécia", "Suiça", "Suriname", "Tailândia", "Taiwan", "Tanzânia", "Togo", "Trinidad &amp; Tobago", "Tunísia",
		"Turquia", "Ucrânia", "Uganda", "Uruguai", "Venezuela", "Vietnã", "Zaire", "Zâmbia", "Zimbábue"
	);
}

// MASK
// EXEMPLO DE USO: $doc = mask("###.###.###-##", $doc);

function mask($mask, $str) {
  $str = str_replace(" ", "", $str);

  for ($i = 0; $i < strlen($str); $i++) {
    $mask[strpos($mask, "#")] = $str[$i];
  }

  return $mask;
}

// ============== FUNÇÃO CHECAR SE O TEM LINK OU CONTEÚDO SUSPEITO PARA EVITAR SPAM ===========
// ============================================================================================

function checarSpam($campo){

	if( (strpos($campo, 'https') !== false) || (strpos($campo, 'http') !== false) || (strpos($campo, '.com') !== false) || (strpos($campo, '?????') !== false) ){
	  echo json_encode(array(
      'status' => 'erro',
			'erro' => 'Informações inseridas de forma inválida, por favor, não inserir links nos campos. Tente novamente.'
		)); exit;
	}

}


?>
