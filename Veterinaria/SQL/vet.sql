-- Tabela de usuários para login
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Tabela para marcações/consultas
CREATE TABLE agendamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nome_dono VARCHAR(100),
  telefone VARCHAR(20),
  email_contato VARCHAR(100),
  idade INT,
  nome_animal VARCHAR(100),
  tipo_animal VARCHAR(50),
  porte VARCHAR(50),
  raca VARCHAR(100),
  idade_animal INT,
  genero VARCHAR(10),
  saude TEXT,
  data_consulta DATE,
  horario_consulta TIME,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);