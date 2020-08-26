<?php

$selicData = [
    "initialDate" => "",
    "endDate" => "",
    "percentage" => "",
    "value" => "12",
];
function selic_info(?array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            if (empty($data['initialDate']) || empty($data['endDate']) || empty($data['percentage']) || empty($data['value'])) {
                $json['message'] = "Informe todos os dados";
                echo json_encode($json);
                return;
            }

            if ($data['percentage'] < 0 || $data['percentage'] > 200) {
                $json['message'] = "O campo 'percentual' deve estar entre os valores 0 e 199,99";
                echo json_encode($json);
                return;
            }

            if ($data['initialDate'] < "1994-07-04" || $data['endDate'] > date('Y-m-d')) {
                $json['message'] = "Período disponível de 04/07/1994 até 22/06/2020";
                echo json_encode($json);
                return;
            }

            // implementar depois
//            if ($data['endDate'] > date('Y-m-d')) {
//                $data['futureDate'] = $data['endDate'];
//                $data['endDate'] = date('Y-m-d');
//            }

            if (isset($data['futureDate'])) {
                $json['message'] = "Implementar o cálculo depois";
                echo json_encode($json);
                return;
            }

            $url = "https://calculadorarendafixa.com.br/calculadora/di/calculo?";
            $endpoint = "dataInicio={$data['initialDate']}&dataFim={$data['endDate']}&percentual={$data['percentage']}&valor={$data['value']}";

            $contents = file_get_contents($url.$endpoint);
            $diJson = json_decode($contents);

            if (!isset($diJson->valorCalculado)) {
                $json['message'] = "Não foi possível realizar o cálculo tente novamente mais tarde";
                echo json_encode($json);
                return;
            }

            $json['message'] = "Valor calculado com sucesso!";
            $json['more'] = true;
            $json['infoResponse'] = $diJson;
            echo json_encode($json);
            return;
    }

    selic_info($selicData);
?>