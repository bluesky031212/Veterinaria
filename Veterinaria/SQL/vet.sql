-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    telefone VARCHAR(9),
    nif VARCHAR(9) UNIQUE,
    idade SMALLINT UNSIGNED CHECK (idade BETWEEN 0 AND 150),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255),
    aceitou_politica BOOLEAN DEFAULT FALSE
);

-- Tabela de animais, relacionando com usuários
CREATE TABLE animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    nome_animal VARCHAR(100),
    tipo_animal VARCHAR(50),
    porte_animal VARCHAR(50),
    raca_animal VARCHAR(50),
    idade_animal SMALLINT UNSIGNED CHECK (idade_animal BETWEEN 0 AND 50),
    genero_animal VARCHAR(10),
    saude_animal BOOLEAN,
    saude_detalhe TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


CREATE TABLE veterinarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    telefone VARCHAR(9),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255)
);


CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    animal_id INT,
    data_consulta DATE,
    hora_consulta TIME,
    veterinario_id INT, 
    descricao_consulta TEXT,
    status_consulta ENUM('agendada', 'realizada', 'cancelada') DEFAULT 'agendada',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE,
    FOREIGN KEY (veterinario_id) REFERENCES veterinarios(id) ON DELETE CASCADE  -- Referência correta ao id
);