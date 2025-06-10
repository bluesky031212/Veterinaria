CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    telefone VARCHAR(9),
    nif VARCHAR(9) UNIQUE,
    idade smallint(3),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(30)
);

CREATE TABLE animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    nome_animal VARCHAR(100),
    tipo_animal VARCHAR(50),
    porte_animal VARCHAR(50),
    raca_animal VARCHAR(50),
    idade_animal smallint(2),
    genero_animal VARCHAR(10),
    saude_animal boolean,
    saude_detalhe TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT,
    data_consulta DATE,
    horario_consulta TIME,
    FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE
);