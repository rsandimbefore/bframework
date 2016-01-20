<?php

/**
 * Classe de tratamento e valida��o de valores
 *
 * @author zucareli
 */

namespace BFramework\Models;

use BPDO;

class Validador {

	private $conexao;

	function __construct(BPDO $con = null) {

		$this->conexao = BPDO::obtemInstancia();

	}

	/**
	 * Verifica no banco de dados quais campos s�o obrigat�rios e valida os dados passados por par�metro se eles est�o nulos ou n�o.
	 * Al�m disso, valida campos de Data e Hora para n�o permitir valores incorretos.
	 * @param array $dados - dados preenchidos no formul�rio j� tratados
	 * @param string $table - nome da tabela que ser� consultada
	 * @param array $avisosEsp - array com os avisos a serem mostrados (� diferente para cada m�dulo)
	 * @param array $drop - array com os campos de tipo dropDown
	 */
	public function validaCampos($dados, $table, $avisosEsp=array(), $drop=array()) {

		$focus = '';

		$metaCols = $this->conexao->MetaColumns($table);

		$avisos = $avisosEsp;

		foreach($dados as $i => $valor) {

			$iCampo = $i;

			$class = 'alerta';
			if(count($drop) > 0) {
				if(in_array($i, $drop)) {
					$iCampo .= "_aux";
					$class = 'ui-autocomplete-input dropdownAlerta';
				}
			}

			if(((trim($valor) == '' || $valor === 0) && $metaCols[strtoupper($i)]->not_null == 1) || count($avisosEsp[$i]) > 0) {

				list($avisos, $focus) = $this->verificaCamposObrigatorios($metaCols, $i, $avisos, $iCampo, $valor, $class, $focus);

			} elseif($metaCols[strtoupper($i)]->type == "NEWDECIMAL") {

				$this->converterParaDecimal($dados, $valor, $i);

			} elseif($metaCols[strtoupper($i)]->type == "DATE") {

				list($ano, $mes, $dia, $avisos, $focus) = $this->verificaDataInvalida($valor, $avisos, $iCampo, $focus);

			} elseif($metaCols[strtoupper($i)]->type == "DATETIME") {

				list($avisos, $focus) = $this->verificaDataHoraInvalida($valor, $avisos, $iCampo, $focus);

			} else {

				if($class == 'dropdownAlerta')
					echo "document.getElementById('".$iCampo."').className='ui-autocomplete-input';";
				else
					echo "document.getElementById('".$iCampo."').className='';";

				echo "$('#".$iCampo."Aviso').attr('data-hint', '');";

			}

			if(count($avisosEsp[$i]) > 0) {
				echo "document.getElementById('".$iCampo."').className='$class'; ";

				if($focus == '')
					$focus = $iCampo;
			}
		}

		if($focus != '')
			echo "document.getElementById('".$focus."').focus();";

		if(count($avisos) > 0) {
			$this->mostrarAvisos($avisos);
			//die();
		}
	}

	/**
	 * Valida os campos que est�o nulos, mas n�o verifica de acordo com o banco de dados e sim todos os campos passados no array.
	 * @param type $dados - Array que ser� validado
	 * @param array $avisosEsp - array com os avisos a serem mostrados (� diferente para cada m�dulo)
	 * @param type $div - ID da div que ser� aberta (show)
	 * @param type $direcao - Caso a div n�o pode ser referenciada com um identificador �nico, voc� tem a op��o de pegar
	 * uma div anterior ou posterior e dizer pra qual dire��o ele deve apontar, sendo elas:
	 * 1 - Posterior
	 * 2 - Anterior
	 */
	public static function validaDados($dados, $avisosEsp=array(), $div="", $direcao="") {
		$focus = '';

		$avisos = $avisosEsp;

		if($dados) {
			foreach($dados as $i => $valor) {
				if($valor == '') {
					echo "
						if($('#".$i."')[0]){
							var str = '".$i."';

							if(str.indexOf('_aux') != (-1))
								document.getElementById('".$i."').className='ui-autocomplete-input dropdownAlerta';
							else if($('#".$i."').hasClass('textarea'))
								document.getElementById('".$i."').className='textarea textareaAlerta';
							else
								document.getElementById('".$i."').className='alerta';
						}";

					$avisos[$i][] = 'Campo obrigat�rio';

					if($focus == '')
						$focus = $i;

				} else {
					echo "
						if($('#".$i."')[0]){
							var str = '".$i."';

							if(str.indexOf('_aux') != (-1))
								document.getElementById('".$i."').className='ui-autocomplete-input';
							else if($('#".$i."').hasClass('textarea'))
								document.getElementById('".$i."').className='textarea';
							else
								document.getElementById('".$i."').className='';
						}";
				}
			}
		}

		if($focus != '') {
			if($div != '') {
				if($direcao != '') {

					if($direcao == 1)
						echo "$('#".$div."').next().show('fast');";
					elseif($direcao == 2)
						echo "$('#".$div."').prev().show('fast');";

				} else {
					echo "$('#tabsInfo').tabs({ selected: ".$div." });";
				}
			}

			echo "document.getElementById('".$focus."').focus();";

		} else {
			if($div != '') {
				if($direcao != '') {

					if($direcao == 1)
						echo "$('#".$div."').next().hide('fast');";
					elseif($direcao == 2)
						echo "$('#".$div."').prev().hide('fast');";
				}
			}

		}

		if(count($avisos) > 0) {
			Valida::mostrarAvisos($avisos);
			die();
		}
	}

	/**
	 * Mostra todos os avisos de erros nos campos do formul�rio
	 * @param array $avisos
	 */
	public static function mostrarAvisos($avisos) {

		if(count($avisos) > 0) {
			foreach($avisos as $id => $mensagem) {
				if(is_array($mensagem))
					$mensagem = implode(";\\n", $mensagem);

				if($mensagem != '') {
					echo "if($('#".$id."Aviso').hasClass('hint--always') === false) { ";
					echo "if(('".$id."').indexOf('data') != (-1)) ";
					echo "$('#".$id."').attr('onchange', \"if(this.value != '') { $('#".$id."').removeClass('alerta'); $('#".$id."').removeClass('dropdownAlerta'); $('#".$id."').removeClass('textareaAlerta'); $('#".$id."Aviso').removeClass('hint--always'); }\" + ($('#".$id."').attr('onchange') != undefined ? $('#".$id."').attr('onchange') : ''));";
					echo " else ";
					echo "$('#".$id."').attr('onblur', \"if(this.value != '') { $('#".$id."').removeClass('alerta'); $('#".$id."').removeClass('dropdownAlerta'); $('#".$id."').removeClass('textareaAlerta'); $('#".$id."Aviso').removeClass('hint--always'); }\" + ($('#".$id."').attr('onblur') != undefined ? $('#".$id."').attr('onblur') : ''));";
					echo "}";
					echo "$('#".$id."Aviso').addClass('hint--always');";
					echo "$('#".$id."Aviso').attr('data-hint', '$mensagem;');";
					echo "$('#".$id."Aviso').attr('style', 'display: block !important');";
				}
			}
		}
	}

	/**
	 * Converte um vetor em uma string, colocando os valores separados pelo s�mbolo definido.
	 * @param array $dados - array com os dados j� tratados
	 * @param string $separador - define o s�mbolo para separar cada valor (Valor Default = '&')
	 * @param boolean $indice - determina se ser� colocado o �ndice junto com o valor (Valor Default = true)
	 * @param string $separadorIndice - define o s�mbolo para separar o �nidce do valor (Valor Default = '=')
	 * @return string
	 */
	public static function getDadosString($dados, $separador="&", $indice=true, $separadorIndice="=") {

		$string = '';
		$x = 1;

		foreach($dados as $index => $valor) {

			$valor = urlencode($valor);

			if ($valor != '') {
				if ($x != 1)
					$string .= $separador;

				if($indice)
					$string .= $index.$separadorIndice;

				$string .= "$valor";

				$x++;
			}
		}

		return $string;
	}

	/**
	 * Tira caracteres de campos normalmente utilizado em CPF e CNPJ
	 * @param type $valor
	 * @return type
	 */
	public static function trataCaracteres($valor){

		if($valor!=""){
			$search =array(".","/","-"," ");
			$valor = str_replace($search, "",$valor);
		}

		return $valor;

	}

	/**
	 * Retorna �cone da mensagem de sucesso
	 * @return string
	 */
	public static function retornaIconeSucesso(){
		return '<img src="images/bt_ok.png" class="png">';
	}

	/**
	 * Retorna �cone da mensagem de sucesso
	 * @return string
	 */
	public static function retornaIconeAlerta(){
		return '<img src="images/bt_alerta2.png" class="png">';
	}

	/**
	 * Retorna �cone da mensagem de erro
	 * @return string
	 */
	public static function retornaIconeErro(){
		return '<img src="images/bt_alerta.png" class="png">';
	}

	/**
	 * Retorna o valor da vari�vel caso o campo n�o seja igual a $valorVar, se for vazio retorna o par�metro $valor
	 * @param string $var
	 * @param string $valorVar
	 * @param string $valor
	 * @return string
	 */
	public static function seIgual($var, $valorVar, $valor){
		return ($var==$valorVar) ? $valor : $var;
	}

	/**
	 * Retorna o valor da vari�vel caso o campo n�o seja vazio, se for vazio retorna o par�metro $valor
	 * @param string $var
	 * @param string $valor
	 * @return string
	 */
	public static function seVazio($var, $valor){
		return ($var=='') ? $valor : $var;
	}

	/**
	 * Retorna a string tratada com rela��o �s aspas (' => \' || " => \") e sem espa�os em branco no in�cio ou fim
	 * @param string $str
	 * @return string
	 */
	public static function trataString($str){
		return addslashes(trim($str));
	}

	/**
	 * For�a uma string para inteiro
	 * @param string $str
	 * @return string
	 */
	public static function trataInteiro($str) {
		return intval($str);
	}

	/**
	 * For�a uma string para float
	 * @param string $str
	 * @return string
	 */
	public static function trataFloat($str) {

		$str = floatval($str);

		return number_format($str, 2, '.', '');
	}

	/**
	 * Retorna a string tratada com rela��o a valores reais (Ex.: Para a entrada R$ 10,00, o m�todo retorna 10.00)
	 * @param string $str
	 * @return string
	 */
	public static function trataMoeda($str) {

		if($str == '')
			return 0.00;

		$strTratada = str_replace(array('R$', '.'), '', $str);
		$strLimpa = Valida::trataString($strTratada);
		$strMoeda = Valida::trataFloat(str_replace(',', '.', $strLimpa));

		return $strMoeda;
	}

	/**
	 * M�todo que converte c�digos ASCII para os caracteres padr�o
	 * @param string $codigo
	 * @return string
	 */
	public static function corrigeCodigoZebra($codigo){

		$tmp0 = Valida::trataString($codigo);
		$tmp1 = str_replace("&#34;", '"', $tmp0);
		$tmp2 = str_replace("&#60;", '<', $tmp1);
		$codigoConvertido = str_replace("&#62;", '>', $tmp2);

		return $codigoConvertido;
	}

	public static function verificaNumAcessoValido($numAcesso){

		return verificaNumAcessoValido($numAcesso);
	}

	/**
	 * Verifica se string � um e-mail valido
	 * @param type $email
	 * @return int
	 */
	public static function validaEmail($email){

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return 0;
		}

		return 1;

	}

	/**
	 *
	 * @return BPDO
	 */
	public function getConexao() {
		return $this->conexao;
	}

	public function setConexao(BPDO $conexao) {
		$this->conexao = $conexao;
	}

	private function verificaCamposObrigatorios($metaCols, $i, $avisos, $iCampo, $valor, $class, $focus) {
		if ($metaCols[strtoupper($i)]->type == "DATETIME") {
			$avisos[$iCampo . "_hora"][] = 'Campo obrigat�rio';
			echo "document.getElementById('" . $iCampo . "_hora').className='alerta'; ";
		}

		if ((trim($valor) == '' || $valor === 0) && $metaCols[strtoupper($i)]->not_null == 1) {
			$avisos[$iCampo][] = 'Campo obrigat�rio';
			echo "document.getElementById('" . $iCampo . "').className='$class'; ";
		}

		if ($focus == '') $focus = $iCampo;return array($avisos, $focus);
		return array($avisos, $focus);
	}

	private function converterParaDecimal($dados, $valor, $i) {
		if ($valor != '') {
			$dados[$i] = str_replace('.', '', $valor);
			$dados[$i] = str_replace(',', '.', $dados[$i]);
		}
	}

	private function verificaDataInvalida($valor, $avisos, $iCampo, $focus) {
		if (trim($valor) != '') {

			list($ano, $mes, $dia) = explode('-', $valor);
			if (!checkdate($mes, $dia, $ano)) {
				$avisos[$iCampo][] = 'Data inv�lida';
				echo "document.getElementById('" . $iCampo . "').className='alerta';";

				if ($focus == '') $focus = $iCampo;
			}
			echo "document.getElementById('" . $iCampo . "').className='';";
			return array($ano, $mes, $dia, $avisos, $focus);
		}
		return array($ano, $mes, $dia, $avisos, $focus);
	}

	private function verificaDataHoraInvalida($valor, $avisos, $iCampo, $focus) {
		if (trim($valor) != '') {

			list($data, $hora) = explode(' ', $valor);
			list($ano, $mes, $dia) = explode('-', $data);
			list($h, $m) = explode(':', $hora);

			if (!checkdate($mes, $dia, $ano)) {
				$avisos[$iCampo][] = 'Data inv�lida';
				echo "document.getElementById('" . $iCampo . "').className='alerta';";

				if ($focus == '') $focus = $iCampo;
			}

			if (($h < 0 or $h > 23) or ($m < 0 or $m > 59)) {
				$avisos[$iCampo][] = 'Hora inv�lida';
				echo "document.getElementById('" . $iCampo . "_hora').className='alerta';";

				if ($focus == '') $focus = $iCampo . "_hora";
			}

			echo "document.getElementById('" . $iCampo . "').className='';";
			echo "document.getElementById('" . $iCampo . "_hora').className=''; ";
			return array($avisos, $focus);
		}
		return array($avisos, $focus);
	}
}

?>
