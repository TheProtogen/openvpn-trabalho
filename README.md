# openvpn-trabalho

Esse sistema foi criado para que cada funcionário tenha seu próprio certificado de VPN, sem precisar compartilhar com outras pessoas. Ele permite criar, baixar e revogar certificados com segurança e controle. Além disso, segue boas práticas como controle de acesso e segmentação de rede.

## Estrutura da solução:

| Máquina  | IP        | Função                    | Tecnologias  |
| -------- | --------- | ------------------------- | ------------ |
| Firewall | 10.0.0.1  | Controle de acesso        | nftables     |
| OpenVPN  | 10.0.0.10 | Servidor VPN + Painel Web | Apache2, PHP |
| Database | 10.0.0.20 | Banco de dados            | MySQL        |

Comunicação:

    --> O Firewall acessa tanto o OpenVPN quanto o Database.
    
    --> OpenVPN e Database se comunicam entre si.
    
    --> O acesso externo é restrito para garantir segurança.

De acordo com o enunciado, a arquitetura usa 3 máquinas virtuais, cada uma com uma função específica:

| Porta | Protocolo | Destino | Uso                             |
| ----- | --------- | ------- | ------------------------------- |
| 80    | TCP       | OpenVPN | Redirecionamento para HTTPS     |
| 443   | TCP       | OpenVPN | Painel Web                      |
| 1194  | UDP       | OpenVPN | Conexão VPN                     |

Todos os outros acessos externos são bloqueados.

## O que o Painel Oferece
Certificados

    --> Criar: Gera um certificado único (.zip) válido por 7 dias.

    --> Listar: Mostra os certificados criados, com filtros.

    --> Baixar: Apenas o administrador logado consegue baixar.

    --> Excluir: Revoga o certificado e remove o arquivo.

Usuários

    --> Cadastro e lista: Adição de novos administradores.

    --> Login seguro: Com senhas criptografadas.

    --> Proteção: Somente usuários autenticados acessam as páginas.
