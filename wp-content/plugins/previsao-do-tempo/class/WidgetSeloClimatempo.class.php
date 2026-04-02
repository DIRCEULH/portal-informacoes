<?php

/*
 * Classe Extendida do core do WP - WP_Widget
 * Criado por: Climatempo
 * E-mail: portal@climatempo.com.br
 * Data: 26-03-2014
 * 
 */

class WidgetSeloClimatempo extends WP_Widget
{
    public $opcoes = null;
    #Estados do Brasil
    public $estados = array("AC", "AL", "AM", "AP", "BA", "CE", "DF", "ES", "GO", "MA", "MG", "MS", "MT", "PA", "PB", "PR", "PE", "PI", "RJ", "RS", "RN", "RO", "RR", "SC", "SP", "SE", "TO");


    function __construct()
    {
        parent::__construct(
            'selo_previsao_do_tempo', // Base ID
            __('Selo de Previsão do Tempo', 'selo_ct_domain'), // Name
            array('description' => __('Selo de Previsão do Tempo', 'selo_ct_domain'),) // Args
        );
    }


    /*
     * FUNCAO COM O CODIGO FONTE EXIBIDO NA PAGINA DO BLOG
    * */
    function widget($args, $instance)
    {
        #tratando os dados informados para o widget
        $link = (isset($instance["tamanhoSkin"]) && $instance["tamanhoSkin"] != '120') ? '' : '120';

        if ($instance["cidade1"] != "" || $instance["cidade2"] != "" || $instance["cidade3"] != "" || $instance["cidade4"] != "" || $instance["cidade5"] != "") {
            $cid = '';
            for ($i = 1; $i < 6; $i++) {
                $cidadesIframe = explode("|", $instance["cidade" . $i]);
                if ($cidadesIframe[0] != "") {
                    if ($cid != '') $cid .= ",";

                    $cid .= $cidadesIframe[0];
                }

            }
        }
        if (!isset($cid) || $cid == "") {
            $cid = "343,347,6,232,25,39,593,256,264,334,94,259,60,56,384,8,88,218,212,61,558,107,84,321,363,377,271";
        }
        ?>
        <div class="widget" id="seloprevisao">
            <iframe
                src='http://selos.climatempo.com.br/selos/MostraSelo<?php echo $link; ?>.php?ORIGEM=WPRESS&CODCIDADE=<?php echo $cid; ?>&SKIN=<?php echo $instance["skinSelo"]; ?>'
                scrolling='no' frameborder='0' width='<?php echo $instance["tamanhoSkin"]; ?>' height='170'
                marginheight='0' marginwidth='0'></iframe>
        </div>
    <?php

    }


    /*
     * FUNCAO QUE RETORNA OS DADOS DE POST PRO WP ARMAZENA-LOS EM BANCO DE DADOS
     * */
    function update($new_instance, $old_instance)
    {
        if (is_array($new_instance)) {
            foreach ($new_instance as $ind => $val) {
                $instance[$ind] = strip_tags($new_instance[$ind]);
            }
        }

        return $instance;

    }


    /*
     * FUNCAO QUE MONTA O FORMULARIO DE CONFIGURACAO DO WIDGET NO ADMIN
    */
    function form($instance)
    {
        if (!empty($_POST))
            echo "<script>location.reload();</script>";

        $flagErro = ($instance["tamanhoSkin"] == "") ? 1 : 0;

        isset($instance['title']) ? $title = $instance['title'] : $title = __('Selo de Previsão do Tempo', 'selo_ct_domain');

        if (!isset($instance['skinSelo'])) $instance['skinSelo'] = 'padrao';

        if (!isset($instance['tamanhoSkin'])) $instance['tamanhoSkin'] = '150';

        if ($instance["cidade1"] != "" || $instance["cidade2"] != "" || $instance["cidade3"] != "" || $instance["cidade4"] != "" || $instance["cidade5"] != "") {
            $cid = '';
            for ($i = 1; $i < 6; $i++) {
                $cidadesIframe = explode("|", $instance["cidade" . $i]);
                if ($cidadesIframe[0] != "") {
                    if ($cid != '') $cid .= ",";
                    $cid .= $cidadesIframe[0];
                }
            }
        }
        if (!isset($cid) || $cid == "") {
            $cid = "343,347,6,232,25,39,593,256,264,334,94,259,60,56,384,8,88,218,212,61,558,107,84,321,363,377,271";
        }

        $estado = (!isset($instance["estado"]) || $instance["estado"] == "") ? "vazio" : $instance["estado"];

        $iframeSkin150 = 'http://selos.climatempo.com.br/selos/MostraSelo.php?CODCIDADE=' . $cid . '&amp;SKIN=' . $instance['skinSelo'];
        $iframeSkin120 = 'http://selos.climatempo.com.br/selos/MostraSelo120.php?CODCIDADE=' . $cid . '&amp;SKIN=' . $instance['skinSelo'];

        ?>
        <script type='text/javascript'
                src='<?php echo plugins_url('js/' . $this->get_field_id('widget'), dirname(__FILE__)); ?>.js'></script>
        <style>
            /* CSS WIDGET PREVISAO DO TEMPO */
            .top10 {
                margin-top: 10px;
            }

            .bottom10 {
                margin-bottom: 10px;
            }

            .padding-10 {
                padding: 10px;
            }

            .padding-left-15 {
                padding-left: 15px;
            }

            .padding-laterais-15 {
                padding: 0 15 0 15;
            }

            .pointer {
                cursor: pointer;
            }

            .font-600 {
                font-weight: 600;
            }

            .txt-center {
                text-align: center;
            }

            .disline {
                display: inline;
            }

            .fundo-ativa {
                background-color: #dcdcdc;
            }

            .fundo-inativo {
                background-color: #f5f5f5;
            }

            .left {
                float: left;
            }

            .disnone {
                display: none;
            }

            .width100 {
                width: 100px;
            }

            .width200 {
                width: 200px;
            }

            .listtpnone {
                list-style-type: none;
            }

            .clear {
                clear: both;
            }

            .selectmultiplo {
                height: 105px;
                width: 220px;
            }

            .red {
                color: #D11111;
            }

            /* FIM CSS WIDGET PREVISAO DO TEMPO */
        </style>
        <!-- HTML DO FORMULÁRIO DE GERAÇÃO DO WIDGET -->
        <div class="padding-10 bottom10">
            <p>Monte o seu Selo:</p>

            <div class="top10">
                <select class="tamanhoSkin" id="<?php echo $this->get_field_id('tamanhoSkin'); ?>"
                        name="<?php echo $this->get_field_name('tamanhoSkin'); ?>">
                    <option value="150" <?php if ($instance['tamanhoSkin'] == '150'){ ?>selected<?php } ?>>150x170
                    </option>
                    <option value="120" <?php if ($instance['tamanhoSkin'] == '120'){ ?>selected<?php } ?>>120x170
                    </option>
                </select>
            </div>
            <div class="top10">
                <div id="<?php echo $this->get_field_id('150x170'); ?>"
                     class="<?php if ($instance['tamanhoSkin'] != '150') print "disnone" ?>">
                    <iframe src="<?php echo $iframeSkin150; ?>" scrolling="no" frameborder="0" width="150" height="170"
                            marginheight="0" marginwidth="0"></iframe>
                </div>
                <div id="<?php echo $this->get_field_id('120x170'); ?>"
                     class="<?php if ($instance['tamanhoSkin'] != '120') print "disnone" ?>">
                    <iframe src="<?php echo $iframeSkin120; ?>" scrolling="no" frameborder="0" width="120" height="170"
                            marginheight="0" marginwidth="0"></iframe>
                </div>
            </div>
            <div class="top10">
                <label for="<?php echo $this->get_field_id('skinSelo'); ?>">Selecione uma Skin:</label><br>
                <select class="skinSelo top10" id="<?php echo $this->get_field_id('skinSelo'); ?>"
                        name="<?php echo $this->get_field_name('skinSelo'); ?>">
                    <option
                        value="padrao"  <?php if ($instance['skinSelo'] == "padrao")    print "selected=\"selected\"" ?>>
                        Padrão
                    </option>
                    <option
                        value="azul"    <?php if ($instance['skinSelo'] == "azul")      print "selected=\"selected\"" ?>>
                        Azul
                    </option>
                    <option
                        value="laranja" <?php if ($instance['skinSelo'] == "laranja")   print "selected=\"selected\"" ?>>
                        Laranja
                    </option>
                    <option
                        value="preto"   <?php if ($instance['skinSelo'] == "preto")     print "selected=\"selected\"" ?>>
                        Preto
                    </option>
                    <option
                        value="verde"   <?php if ($instance['skinSelo'] == "verde")     print "selected=\"selected\"" ?>>
                        Verde
                    </option>
                </select>
            </div>

            <div class="top10">
                <label for="<?php echo $this->get_field_name('estado'); ?>">Estado: </label><br>
                <select name="<?php echo $this->get_field_name('estado'); ?>"
                        id="<?php echo $this->get_field_id('estado'); ?>" class="estado top10"
                        dirname="<?php echo plugins_url(); ?>" tabindex="3">
                    <option value="vazio" <?php if ($estado === "vazio") print 'selected="selected"' ?>>--</option>
                    <?php foreach ($this->estados as $ind => $value) { ?>
                        <option
                            value="<?php echo $ind; ?>" <?php if ($estado == $ind && $estado !== "vazio") print "selected=\"selected\""; ?>><?php echo $value; ?></option>";
                    <?php } ?>
                </select>
            </div>
            <div class="top10 <?php if ($flagErro == 1) echo "divFrom"; ?>">
                <label for="<?php echo $this->get_field_id('cidadeFrom'); ?>">Selecione até 5 cidades: </label><br>
                <select name="<?php echo $this->get_field_name('cidadeFrom'); ?>"
                        id="<?php echo $this->get_field_id('cidadeFrom'); ?>" multiple
                        class="cidadeFrom selectmultiplo top10" tabindex="3">
                </select>
            </div>
            <div class="top10">
                <label for="<?php echo $this->get_field_id('cidadeTo'); ?>">Cidades Selecionadas: </label><br>
                <select name="<?php echo $this->get_field_name('cidadeTo'); ?>"
                        id="<?php echo $this->get_field_id('cidadeTo'); ?>" size="5" multiple
                        class="cidadeTo selectmultiplo top10" tabindex="3">
                    <?php
                    for ($i = 1; $i < 6; $i++) {
                        $cidade = "cidade" . $i;
                        if (isset($instance[$cidade])) {
                            $cid = explode("|", $instance[$cidade]);

                            $val = trim($cid[0]);
                            $text = trim($cid[1]);
                            if ($val != "" && $text != "")
                                echo '<option value="' . $val . '">' . $text . '</option>';
                        }
                    }
                    ?>
                </select>

                <div id="<?php echo $this->get_field_id("cids"); ?>">
                    <input type="hidden" id="<?php echo $this->get_field_id('cidade1'); ?>"
                           name="<?php echo $this->get_field_name('cidade1'); ?>"
                           value="<?php if (isset($instance["cidade1"])) print $instance["cidade1"]; ?>"
                           class="cidade1 cidct">
                    <input type="hidden" id="<?php echo $this->get_field_id('cidade2'); ?>"
                           name="<?php echo $this->get_field_name('cidade2'); ?>"
                           value="<?php if (isset($instance["cidade2"])) print $instance["cidade2"]; ?>"
                           class="cidade2 cidct">
                    <input type="hidden" id="<?php echo $this->get_field_id('cidade3'); ?>"
                           name="<?php echo $this->get_field_name('cidade3'); ?>"
                           value="<?php if (isset($instance["cidade3"])) print $instance["cidade3"]; ?>"
                           class="cidade3 cidct">
                    <input type="hidden" id="<?php echo $this->get_field_id('cidade4'); ?>"
                           name="<?php echo $this->get_field_name('cidade4'); ?>"
                           value="<?php if (isset($instance["cidade4"])) print $instance["cidade4"]; ?>"
                           class="cidade4 cidct">
                    <input type="hidden" id="<?php echo $this->get_field_id('cidade5'); ?>"
                           name="<?php echo $this->get_field_name('cidade5'); ?>"
                           value="<?php if (isset($instance["cidade5"])) print $instance["cidade5"]; ?>"
                           class="cidade5 cidct">
                </div>
                <div id="<?php echo $this->get_field_id('cidError2'); ?>" class="disnone red">
                    <p>Esta cidade já foi adicionada.</p>
                </div>
                <div id="<?php echo $this->get_field_id('cidError1'); ?>" class="disnone red">
                    <p>Você só pode adicionar até 5 cidades.</p>
                </div>
            </div>
        </div>
        <div class="top10"></div>
        <input type="hidden" id="<?php echo $this->get_field_id('iframeSkin150'); ?>"
               value="<?php echo $iframeSkin150; ?>">
        <input type="hidden" id="<?php echo $this->get_field_id('iframeSkin120'); ?>"
               value="<?php echo $iframeSkin120; ?>">
        <input type="hidden" id="<?php echo $this->get_field_id('skinCor'); ?>"
               value="<?php echo $instance['skinSelo']; ?>">
        <?php if ($instance["estado"] !== null) { ?>
        <script>
            jQuery(document).ready(function ($) {
                $("#<?php echo $this->get_field_id('estado'); ?>").trigger("change");
            });
        </script>
    <?php
    }
    }
}