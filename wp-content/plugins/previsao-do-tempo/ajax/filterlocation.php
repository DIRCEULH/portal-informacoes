<?php

try {
    if (isset($_POST["dir"]) && isset($_POST["uf"])) {
        $file = $_POST["dir"] . "/previsao-do-tempo/xml/estado-cidade.xml";
        $uf = simplexml_load_file($file);
        $options = array();
        try {
            foreach ($uf->estado[(int)$_POST["uf"]]->cidade as $ind => $value) {
                $options[trim((int)$value["id"])] = trim((string)$value["nome"]);
            }

            print json_encode($options);
        } catch (Exception $e) {
            echo "UF: " . $_POST["uf"] . ' exceção: ', $e->getMessage(), "\n";
        }
    }
} catch (Exception $e) {
    echo 'exceção: ', $e->getMessage(), "\n";
}