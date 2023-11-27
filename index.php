
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viacep com Banco de Dados</title>
</head>
<body>  
    <h1>Viacep com Banco de Dados</h1>

    <form action="Viacep.php" method="post">
        <label for="cep">Cep</label>
        <input type="number" name="cep" id="cep" maxlength="8" autocomplete="off">
        <input type="submit" value="Pesquisar Cep / Enviar para o Banco">
    </form>
</body>
</html>

<?php

include_once("conn.php");


if($_SERVER['REQUEST_METHOD'] == 'POST')
{

    $cep = $_POST["cep"];
    
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,$url);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if(!str_contains($response,"{Bad Request}"))
    {
        $endereco = json_decode($response, true);
        
        #var_dump($response);
        if(!isset($endereco['erro']))
        {
            $ceptxt = $endereco['cep'];
            $logradourotxt = $endereco['logradouro'];
            $bairrotxt = $endereco['bairro'];
            $localidadetxt = $endereco['localidade'];
            $uftxt = $endereco['uf'];
            

            //Exibir
            echo "CEP:".$ceptxt."<br>";
            echo "Logradouro: ".$logradourotxt."<br>";
            echo "Bairro: ".$bairrotxt."<br>";
            echo "Cidade: ".$localidadetxt."<br>";
            echo "Estado:  ".$uftxt."<br>";

            //Enviar para o banco
            $pdo = '';
            $pdo = $conn->prepare("INSERT INTO TBCEP VALUES ('',:cep,:logradouro,:bairro,:localidade,:uf)");
            $pdo->bindParam(':cep', $logradourotxt);
            $pdo->bindParam(':logradouro', $logradourotxt);
            $pdo->bindParam(':bairro', $bairrotxt);
            $pdo->bindParam(':localidade', $localidadetxt);
            $pdo->bindParam(':uf', $uftxt);
            
            try
            {
                $pdo->execute();
                echo "<br>Enviado para o banco de dados com sucesso";
                $_SERVER['REQUEST_METHOD'] = '';
            }
            catch(PDOException $e)
            {
                echo "<br>Não foi possível enviar para o banco de dados";
                echo "<br>Erro original: $e";
            }
        }
        else
        {
            echo "Cep inexistente";
        }
    }
    else
    {
        echo "Cep inválido";
    }
}
?>
