# openvpn-trabalho

Esse sistema foi criado para que cada funcionário tenha seu próprio certificado de VPN, sem precisar compartilhar com outras pessoas. Ele permite criar, baixar e revogar certificados com segurança e controle. Além disso, segue boas práticas como controle de acesso e segmentação de rede.

## Prints das telas

[Link dos prints](prints/prints.md)

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

## Configuração das Máquinas

Todas as VMs:

    --> Configurar rede em /etc/network/interfaces.

    --> Usuário padrão: usuario | Senha: 123456

Firewall:

    --> Rede configurada com IP fixo: 10.0.0.1

    --> Regras de firewall no arquivo /etc/nftables.conf

OpenVPN:

    --> IP fixo: 10.0.0.10

    --> Instalar Apache, PHP e OpenVPN.

    --> Colocar os arquivos do painel em /var/www/html/.

Database:

    --> IP fixo: 10.0.0.20

    --> Instalar MySQL Server.

    --> Criar o banco e tabelas para usuários e certificados.

Acesso ao Painel Web

    --> No Firewall, rode ip a para pegar o IP da interface enp0s8 (interface externa).

    --> No navegador, acesse:

http://<IP_DO_FIREWALL>/index.php

Login padrão:

    --> Email: admin@protonmail.com

    --> Senha: Admin123!

Estrutura de Pastas do Projeto

Dentro da VM do OpenVPN, em /var/www/html/:

```
/var/www/html/
├── index.php                 
├── views/                    
│   ├── cadastro.php          
│   ├── login.php             
│   ├── logout.php            
│   ├── adms.php              
│   ├── baixar.php            
│   └── certificados.php      
├── includes/                               
│   ├── navbar.php            
│   ├── auth.php              
│   └── funcoes.php           
└── storage/                  
    ├── registros.json        
    ├── usuarios.json        
    └── A1RS972_cert.zip   
```
