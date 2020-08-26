public function selic_info(?array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if (!empty($data['csrf'])) {
            if (!csrf_verify($data)) {
                $json['message'] = $this->message->error("Erro ao enviar, favor use o formulário")->render();
                echo json_encode($json);
                return;
            }

            if (request_limit("calcDI", 4, 60 * 2)) {
                $json['message'] = $this->message->error(
                    "Você já fez 4 solicitações, esse é o limite. Por favor, aguarde 2 minutos para tentar novamente!"
                )->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['initialDate']) || empty($data['endDate']) || empty($data['percentage']) || empty($data['value'])) {
                $json['message'] = $this->message->warning("Informe todos os dados")->render();
                echo json_encode($json);
                return;
            }

            if ($data['percentage'] < 0 || $data['percentage'] > 200) {
                $json['message'] = $this->message->warning("O campo 'percentual' deve estar entre os valores 0 e 199,99")->render();
                echo json_encode($json);
                return;
            }

            if ($data['initialDate'] < "1994-07-04" || $data['endDate'] > date('Y-m-d')) {
//            if ($data['initialDate'] < "1994-07-04") {
                $json['message'] = $this->message->warning("Período disponível de 04/07/1994 até 22/06/2020")->render();
                echo json_encode($json);
                return;
            }

            // implementar depois
//            if ($data['endDate'] > date('Y-m-d')) {
//                $data['futureDate'] = $data['endDate'];
//                $data['endDate'] = date('Y-m-d');
//            }

            if (isset($data['futureDate'])) {
                $json['message'] = $this->message->info("Implementar o cálculo depois")->render();
                echo json_encode($json);
                return;
            }

            $url = "https://calculadorarendafixa.com.br/calculadora/di/calculo?";
            $endpoint = "dataInicio={$data['initialDate']}&dataFim={$data['endDate']}&percentual={$data['percentage']}&valor={$data['value']}";

            $contents = file_get_contents($url.$endpoint);
            $diJson = json_decode($contents);

            if (!isset($diJson->valorCalculado)) {
                $json['message'] = $this->message->warning("Não foi possível realizar o cálculo tente novamente mais tarde")->render();
                echo json_encode($json);
                return;
            }

            $json['message'] = $this->message->success("Valor calculado com sucesso!")->render();
            $json['more'] = true;
            $json['infoResponse'] = $this->infoResponse->selic($diJson)->render();
            echo json_encode($json);
            return;
        }
    }
