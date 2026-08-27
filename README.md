# CS-SCOUT — Esport Intelligence & Player Scouting Platform

O CS-SCOUT é um sistema web completo e profissional focado na análise estatística, comparação e scouting de jogadores competitivos de Counter-Strike. O sistema utiliza uma abordagem analítica avançada, transformando métricas brutas em dados visuais de desempenho, incluindo o algoritmo exclusivo Player DNA.

---

## Equipe de Desenvolvimento

Projeto desenvolvido para a disciplina / projeto de sistemas web pelos integrantes:

* Eduardo — Analista de Dados & Documentação
* João Paulo — Desenvolvedor Backend & Banco de Dados
* Joaquim — Engenheiro de Software & Regras de Negócioe e Desenvolvedor Frontend & UI/UX
* Julia — Analista de Dados & Documentação

---

## Tecnologias Utilizadas

O projeto foi construído estritamente com tecnologias web fundamentais, sem a utilização de frameworks de frontend ou backend:

* Frontend: HTML5, CSS3 (Tema Dark Cyber), JavaScript Puro (ES6+ & HTML5 Canvas API)
* Backend: PHP 8+
* Banco de Dados: MySQL / MariaDB
* Gerenciamento do BD: phpMyAdmin
* Versionamento: Git & GitHub

---

## Funcionalidades Principais

### 1. Dashboard e Visão Geral
* Cards de Indicadores (KPIs): Destaques para o Top Win Rate, Maior KPR, Maior Impact e Maior ADR.
* Top 5 Jogadores: Acesso rápido aos líderes do ranking global.
* Métricas Gráficas: Visualização simplificada da performance dos atletas.

### 2. Player DNA (Funcionalidade Exclusiva)
Algoritmo proprietário que calcula 6 atributos de performance (0 a 100) a partir de estatísticas brutas:
* AIM: Calculado com base em KPR, ADR e Impact.
* IMPACT: Focado em rodadas de abertura, multikills e taxa de vitória.
* CONSISTÊNCIA: Medido por KAST e estabilidade por round.
* SOBREVIVÊNCIA: Baseado no índice de sobrevivência e KAST.
* CLUTCH: Desempenho sob situações de inferioridade numérica.
* FIREPOWER: Poder bruto de dano e eliminações.
* Gráfico Radar Interativo: Desenhado em tempo real com Canvas Nativo JS.
* Player Class: Classificação automática (ex: AWP Master, Entry Fragger, Rifle Machine, Clutch King, Survivor, All-Rounder).

### 3. Comparador Direct (1v1)
* Comparação lado a lado entre dois jogadores selecionados.
* Destaque Visual Automático: Identificação do atleta com vantagem em cada métrica.
* Veredito Inteligente: Conclusão automatizada em linguagem natural sobre o confronto.

### 4. Ranking Global
* Classificação geral atualizada dinamicamente pelo banco de dados.
* Filtros por categoria (Win Rate, KPR, KAST, Impact, ADR, Clutch, Firepower e Consistência).
* Destaque para pódio (1º, 2º, 3º lugar).

### 5. Importação em Lote via Excel/CSV (Painel Admin)
* Leitura e processamento de planilhas de estatísticas (.csv).
* Validação de colunas e atualização automática no MySQL via UPSERT.
* Pré-visualização e relatório de inserções com feedback visual.

### 6. Autenticação, Controle de Acesso & Segurança
* Níveis de permissão distintos: USER e ADMIN.
* Criptografia segura de senhas via password_hash() e password_verify().
* Proteção contra SQL Injection usando Prepared Statements (PDO).
* Proteção de rotas e sanitização de dados HTML contra XSS.

---

## Como Instalar e Executar (XAMPP)

1. Copiar o Projeto:
   Baixe ou clone o repositório na pasta htdocs do seu XAMPP:
   C:\xampp\htdocs\cs-scout

2. Iniciar Serviços:
   Abra o XAMPP Control Panel e inicie o Apache e o MySQL.

3. Configurar o Banco de Dados:
   * Acesse http://localhost/phpmyadmin/
   * Crie um novo banco de dados chamado cs_scout.
   * Importe o arquivo database/cs_scout.sql localizado na raiz do projeto.

4. Acessar o Sistema:
   Acesse no navegador: http://localhost/cs-scout/

---

## Credenciais Padrão de Teste

| Tipo | Usuário | Senha |
| :--- | :--- | :--- |
| Administrador | admin | admin123 |
| Usuário Padrão | Criar via tela de cadastro (cadastro.php) | - |

---

CS-SCOUT — Desenvolvido por Eduardo, João Paulo, Joaquim e Julia.
