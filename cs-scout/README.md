# CS-SCOUT — Esport Intelligence & Player Scouting Platform

CS-SCOUT é um sistema completo de scouting competitivo de Counter-Strike desenvolvido em PHP 8 e Vanilla JavaScript com visual Dark Cyber.

## 🚀 Como Executar o Projeto no XAMPP

1. **Clonar/Mover os arquivos:**
   Copie a pasta `cs-scout` para dentro de `C:\xampp\htdocs\`.

2. **Inicializar o Servidor:**
   Abra o XAMPP Control Panel e inicie os serviços **Apache** e **MySQL**.

3. **Configuração do Banco de Dados:**
   - Acesse `http://localhost/phpmyadmin/`.
   - Crie um novo banco chamado `cs_scout`.
   - Clique na aba **Importar** e selecione o arquivo `cs_scout.sql` localizado na raiz do projeto.

4. **Credenciais Padrão do Administrador:**
   - **Usuário:** `admin`
   - **Senha:** `admin123`

5. **Acessar a Plataforma:**
   Navegue para `http://localhost/cs-scout/index.php`.

## 📁 Funcionalidades

- **Dashboard** com estatísticas gerais
- **Lista de Jogadores** com busca, filtros e favoritos
- **Player DNA** com gráfico Radar em Canvas (HTML5)
- **Comparação** lado a lado entre jogadores
- **Ranking Global** por diversos critérios
- **Favoritos** pessoais por usuário
- **Painel Admin** para importação CSV e gerenciamento de jogadores
- **Sistema de Login/Cadastro** completo

## ⚠️ Requisitos

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Extensão PDO do PHP habilitada
