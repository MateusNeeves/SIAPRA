<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paises', function (Blueprint $table) {
            $table->string('nome')->primary();
        });

        DB::insert("INSERT INTO PAISES (NOME) VALUES ('AFEGANISTÃO'), ('ACROTÍRI E DECELIA'), ('ÁFRICA DO SUL'), ('ALBÂNIA'), ('ALEMANHA'), ('AMERICAN SAMOA'), ('ANDORRA'), ('ANGOLA'), ('ANGUILLA'), ('ANTÍGUA E BARBUDA'), ('ANTILHAS NEERLANDESAS'), ('ARÁBIA SAUDITA'), ('ARGÉLIA'), ('ARGENTINA'), ('ARMÉNIA'), ('ARUBA'), ('AUSTRÁLIA'), ('ÁUSTRIA'), ('AZERBAIJÃO'), ('BAHAMAS'), ('BANGLADECHE'), ('BARBADOS'), ('BARÉM'), ('BASSAS DA ÍNDIA'), ('BÉLGICA'), ('BELIZE'), ('BENIM'), ('BERMUDAS'), ('BIELORRÚSSIA'), ('BOLÍVIA'), ('BÓSNIA E HERZEGOVINA'), ('BOTSUANA'), ('BRASIL'), ('BRUNEI DARUSSALAM'), ('BULGÁRIA'), ('BURQUINA FASO'), ('BURUNDI'), ('BUTÃO'), ('CABO VERDE'), ('CAMARÕES'), ('CAMBOJA'), ('CANADÁ'), ('CATAR'), ('CAZAQUISTÃO'), ('CENTRO-AFRICANA REPÚBLICA'), ('CHADE'), ('CHILE'), ('CHINA'), ('CHIPRE'), ('COLÔMBIA'), ('COMORES'), ('CONGO'), ('CONGO REPÚBLICA DEMOCRÁTICA'), ('COREIA DO NORTE'), ('COREIA DO SUL'), ('COSTA DO MARFIM'), ('COSTA RICA'), ('CROÁCIA'), ('CUBA'), ('DINAMARCA'), ('DOMÍNICA'), ('EGIPTO'), ('EMIRADOS ÁRABES UNIDOS'), ('EQUADOR'), ('ERITREIA'), ('ESLOVÁQUIA'), ('ESLOVÉNIA'), ('ESPANHA'), ('ESTADOS UNIDOS'), ('ESTÓNIA'), ('ETIÓPIA'), ('FAIXA DE GAZA'), ('FIJI'), ('FILIPINAS'), ('FINLÂNDIA'), ('FRANÇA'), ('GABÃO'), ('GÂMBIA'), ('GANA'), ('GEÓRGIA'), ('GIBRALTAR'), ('GRANADA'), ('GRÉCIA'), ('GRONELÂNDIA'), ('GUADALUPE'), ('GUAM'), ('GUATEMALA'), ('GUERNSEY'), ('GUIANA'), ('GUIANA FRANCESA'), ('GUINÉ'), ('GUINÉ EQUATORIAL'), ('GUINÉ-BISSAU'), ('HAITI'), ('HONDURAS'), ('HONG KONG'), ('HUNGRIA'), ('IÉMEN'), ('ILHA BOUVET'), ('ILHA CHRISTMAS'), ('ILHA DE CLIPPERTON'), ('ILHA DE JOÃO DA NOVA'), ('ILHA DE MAN'), ('ILHA DE NAVASSA'), ('ILHA EUROPA'), ('ILHA NORFOLK'), ('ILHA TROMELIN'), ('ILHAS ASHMORE E CARTIER'), ('ILHAS CAIMAN'), ('ILHAS COCOS (KEELING),'), ('ILHAS COOK'), ('ILHAS DO MAR DE CORAL'), ('ILHAS FALKLANDS (ILHAS MALVINAS),'), ('ILHAS FEROE'), ('ILHAS GEÓRGIA DO SUL E SANDWICH DO SUL'), ('ILHAS MARIANAS DO NORTE'), ('ILHAS MARSHALL'), ('ILHAS PARACEL'), ('ILHAS PITCAIRN'), ('ILHAS SALOMÃO'), ('ILHAS SPRATLY'), ('ILHAS VIRGENS AMERICANAS'), ('ILHAS VIRGENS BRITÂNICAS'), ('ÍNDIA'), ('INDONÉSIA'), ('IRÃO'), ('IRAQUE'), ('IRLANDA'), ('ISLÂNDIA'), ('ISRAEL'), ('ITÁLIA'), ('JAMAICA'), ('JAN MAYEN'), ('JAPÃO'), ('JERSEY'), ('JIBUTI'), ('JORDÂNIA'), ('KIRIBATI'), ('KOWEIT'), ('LAOS'), ('LESOTO'), ('LETÓNIA'), ('LÍBANO'), ('LIBÉRIA'), ('LÍBIA'), ('LISTENSTAINE'), ('LITUÂNIA'), ('LUXEMBURGO'), ('MACAU'), ('MACEDÓNIA'), ('MADAGÁSCAR'), ('MALÁSIA'), ('MALAVI'), ('MALDIVAS'), ('MALI'), ('MALTA'), ('MARROCOS'), ('MARTINICA'), ('MAURÍCIA'), ('MAURITÂNIA'), ('MAYOTTE'), ('MÉXICO'), ('MIANMAR'), ('MICRONÉSIA'), ('MOÇAMBIQUE'), ('MOLDÁVIA'), ('MÓNACO'), ('MONGÓLIA'), ('MONTENEGRO'), ('MONTSERRAT'), ('NAMÍBIA'), ('NAURU'), ('NEPAL'), ('NICARÁGUA'), ('NÍGER'), ('NIGÉRIA'), ('NIUE'), ('NORUEGA'), ('NOVA CALEDÓNIA'), ('NOVA ZELÂNDIA'), ('OMÃ'), ('PAÍSES BAIXOS'), ('PALAU'), ('PALESTINA'), ('PANAMÁ'), ('PAPUÁSIA-NOVA GUINÉ'), ('PAQUISTÃO'), ('PARAGUAI'), ('PERU'), ('POLINÉSIA FRANCESA'), ('POLÓNIA'), ('PORTO RICO'), ('PORTUGAL'), ('QUÉNIA'), ('QUIRGUIZISTÃO'), ('REINO UNIDO'), ('REPÚBLICA CHECA'), ('REPÚBLICA DOMINICANA'), ('ROMÉNIA'), ('RUANDA'), ('RÚSSIA'), ('SAHARA OCCIDENTAL'), ('SALVADOR'), ('SAMOA'), ('SANTA HELENA'), ('SANTA LÚCIA'), ('SANTA SÉ'), ('SÃO CRISTÓVÃO E NEVES'), ('SÃO MARINO'), ('SÃO PEDRO E MIQUELÃO'), ('SÃO TOMÉ E PRÍNCIPE'), ('SÃO VICENTE E GRANADINAS'), ('SEICHELES'), ('SENEGAL'), ('SERRA LEOA'), ('SÉRVIA'), ('SINGAPURA'), ('SÍRIA'), ('SOMÁLIA'), ('SRI LANCA'), ('SUAZILÂNDIA'), ('SUDÃO'), ('SUÉCIA'), ('SUÍÇA'), ('SURINAME'), ('SVALBARD'), ('TAILÂNDIA'), ('TAIWAN'), ('TAJIQUISTÃO'), ('TANZÂNIA'), ('TERRITÓRIO BRITÂNICO DO OCEANO ÍNDICO'), ('TERRITÓRIO DAS ILHAS HEARD E MCDONALD'), ('TIMOR-LESTE'), ('TOGO'), ('TOKELAU'), ('TONGA'), ('TRINDADE E TOBAGO'), ('TUNÍSIA'), ('TURKS E CAICOS'), ('TURQUEMENISTÃO'), ('TURQUIA'), ('TUVALU'), ('UCRÂNIA'), ('UGANDA'), ('URUGUAI'), ('USBEQUISTÃO'), ('VANUATU'), ('VENEZUELA'), ('VIETNAME'), ('WALLIS E FUTUNA'), ('ZÂMBIA'), ('ZIMBABUÉ');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
