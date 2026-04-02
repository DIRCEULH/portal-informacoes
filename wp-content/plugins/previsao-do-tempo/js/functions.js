/*
 * Classe Extendida do core do WP - WP_Widget
 * Criada por: Diego Andrade
 * E-mail: diego.andrade@climatempo.com.br
 * Data: 03-04-2014
 * 
 * Copyright 2014 - Climatempo. Todos os direitos reservados. */

function wsctcontaCidade(form) {
    var qtd = 0;
    jQuery("#widget-selo_previsao_do_tempo-" + form + "-cidadeTo option").each(function () {
        //console.log(jQuery(this).val());
        qtd++;
    });

    return qtd;
}

function wsctverificaDuplicidade(cid, form) {
    var flag = 0;
    jQuery("#widget-selo_previsao_do_tempo-" + form + "-cids").children().each(function () {
        if (jQuery(this).val() == cid) {
            jQuery("#widget-selo_previsao_do_tempo-" + form + "-cidError2").removeClass("disnone")
            setTimeout(function () {
                jQuery("#widget-selo_previsao_do_tempo-" + form + "-cidError2").addClass("disnone")
            }, 4000);
            flag++;
        }
    });
    return (flag === 0) ? true : false;
}

function wsctaddCidade(cid, cname, form) {
    var flag = 0;
    jQuery("#widget-selo_previsao_do_tempo-" + form + "-cids").children().each(function () {
        if (jQuery(this).val() == "" && flag == 0) {
            jQuery(this).val(cid + "|" + cname);
            flag++;
        }
    });

    return (flag > 0) ? true : false;
}

function wsctremoveCidade(cid, form) {
    jQuery("#widget-selo_previsao_do_tempo-" + form + "-cids").children().each(function () {
        if (jQuery(this).val() == cid) {
            jQuery(this).val("");
        }
    })
}