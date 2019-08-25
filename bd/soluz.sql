
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `soluz` DEFAULT CHARACTER SET utf8 ;
USE `soluz` ;

CREATE TABLE IF NOT EXISTS `soluz`.`Serie` (
  `Codigo_Serie` INT NOT NULL AUTO_INCREMENT,
  `Descricao` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Codigo_Serie`))
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Disciplinas` (
  `Codigo_Disciplina` INT NOT NULL AUTO_INCREMENT,
  `Nome` VARCHAR(50) NOT NULL,
  `Serie_Codigo_Serie` INT NOT NULL,
  PRIMARY KEY (`Codigo_Disciplina`),
  INDEX `fk_Disciplinas_Serie1_idx` (`Serie_Codigo_Serie` ASC),
  CONSTRAINT `fk_Disciplinas_Serie1`
    FOREIGN KEY (`Serie_Codigo_Serie`)
    REFERENCES `soluz`.`Serie` (`Codigo_Serie`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Alunos` (
  `Matricula` VARCHAR(15) NOT NULL,
  `Nome` VARCHAR(100) NOT NULL,
  `Email` VARCHAR(100) NOT NULL,
  `Data_Nascimento` DATE NOT NULL,
  `Ultimo_Login` DATETIME NULL,
  `Senha` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`Matricula`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `soluz`.`Professores` (
  `Matricula` VARCHAR(20) NOT NULL,
  `Nome` VARCHAR(100) NOT NULL,
  `Email` VARCHAR(100) NOT NULL,
  `Data_Nascimento` DATE NOT NULL,
  `Ultimo_Login` DATETIME NULL,
  `Senha` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`Matricula`))
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Avaliacoes` (
  `Codigo_Avaliacao` INT NOT NULL AUTO_INCREMENT,
  `Conteudo` VARCHAR(50) NOT NULL,
  `Disciplina_Codigo_Disciplina` INT NOT NULL,
  `Data_Inicio` DATETIME NOT NULL,
  `Data_Fim` DATETIME NOT NULL,
  `Peso` VARCHAR(5) NULL,
  `Embaralhar` TINYINT NULL,
  PRIMARY KEY (`Codigo_Avaliacao`),
  INDEX `fk_Avaliacoes_Disciplina1_idx` (`Disciplina_Codigo_Disciplina` ASC),
  CONSTRAINT `fk_Avaliacoes_Disciplina1`
    FOREIGN KEY (`Disciplina_Codigo_Disciplina`)
    REFERENCES `soluz`.`Disciplinas` (`Codigo_Disciplina`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Professores_has_Disciplina` (
  `Professores_Matricula` VARCHAR(20) NOT NULL,
  `Disciplina_Codigo_Disciplina` INT NOT NULL,
  PRIMARY KEY (`Professores_Matricula`, `Disciplina_Codigo_Disciplina`),
  INDEX `fk_Professores_has_Disciplina_Disciplina1_idx` (`Disciplina_Codigo_Disciplina` ASC),
  INDEX `fk_Professores_has_Disciplina_Professores_idx` (`Professores_Matricula` ASC),
  CONSTRAINT `fk_Professores_has_Disciplina_Professores`
    FOREIGN KEY (`Professores_Matricula`)
    REFERENCES `soluz`.`Professores` (`Matricula`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Professores_has_Disciplina_Disciplina1`
    FOREIGN KEY (`Disciplina_Codigo_Disciplina`)
    REFERENCES `soluz`.`Disciplinas` (`Codigo_Disciplina`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `soluz`.`Disciplina_has_Alunos` (
  `Disciplina_Codigo_Disciplina` INT NOT NULL,
  `Alunos_Matricula` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Disciplina_Codigo_Disciplina`, `Alunos_Matricula`),
  INDEX `fk_Disciplina_has_Alunos_Alunos1_idx` (`Alunos_Matricula` ASC),
  INDEX `fk_Disciplina_has_Alunos_Disciplina1_idx` (`Disciplina_Codigo_Disciplina` ASC),
  CONSTRAINT `fk_Disciplina_has_Alunos_Disciplina1`
    FOREIGN KEY (`Disciplina_Codigo_Disciplina`)
    REFERENCES `soluz`.`Disciplinas` (`Codigo_Disciplina`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Disciplina_has_Alunos_Alunos1`
    FOREIGN KEY (`Alunos_Matricula`)
    REFERENCES `soluz`.`Alunos` (`Matricula`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Palavras_Chave` (
  `Codigo_Palavras_Chave` INT NOT NULL AUTO_INCREMENT,
  `Descricao` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`Codigo_Palavras_Chave`))
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Tipo` (
  `Codigo_Tipo` INT NOT NULL AUTO_INCREMENT,
  `Descricao` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Codigo_Tipo`))
ENGINE = InnoDB;

INSERT INTO Tipo VALUES (1, 'Discursiva'), (2, 'Única Escolha'), (3, 'Verdadeiro ou Falso');


CREATE TABLE IF NOT EXISTS `Questao` (
  `Codigo_Questao` INT NOT NULL AUTO_INCREMENT,
  `Enunciado` VARCHAR(100),
  `Texto` TEXT NULL,
  `Tipo_Codigo` INT NOT NULL,
  PRIMARY KEY (`Codigo_Questao`),
  INDEX `fk_Questao_Tipo1_idx` (`Tipo_Codigo` ASC),
  CONSTRAINT `fk_Questao_Tipo1`
    FOREIGN KEY (`Tipo_Codigo`)
    REFERENCES `soluz`.`Tipo` (`Codigo_Tipo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



CREATE TABLE IF NOT EXISTS `soluz`.`Questoes_has_Avaliacoes` (
  `Questoes_Codigo_Questao` INT NOT NULL,
  `Avaliacoes_Codigo_Avaliacao` INT NOT NULL,
  PRIMARY KEY (`Questoes_Codigo_Questao`, `Avaliacoes_Codigo_Avaliacao`),
  INDEX `fk_Questoes_has_Avaliacoes_Avaliacoes1_idx` (`Avaliacoes_Codigo_Avaliacao` ASC),
  INDEX `fk_Questoes_has_Avaliacoes_Questoes1_idx` (`Questoes_Codigo_Questao` ASC),
  CONSTRAINT `fk_Questoes_has_Avaliacoes_Questoes1`
    FOREIGN KEY (`Questoes_Codigo_Questao`)
    REFERENCES `soluz`.`Questao` (`Codigo_Questao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Questoes_has_Avaliacoes_Avaliacoes1`
    FOREIGN KEY (`Avaliacoes_Codigo_Avaliacao`)
    REFERENCES `soluz`.`Avaliacoes` (`Codigo_Avaliacao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Alternativa` (
  `Codigo_Alternativa` INT NOT NULL AUTO_INCREMENT,
  `Descricao` VARCHAR(100) NOT NULL,
  `Correta` TINYINT NOT NULL,
  `Questao_Codigo` INT NOT NULL,
  PRIMARY KEY (`Codigo_Alternativa`),
  INDEX `fk_Alternativa_Questao1_idx` (`Questao_Codigo` ASC),
  CONSTRAINT `fk_Alternativa_Questao1`
    FOREIGN KEY (`Questao_Codigo`)
    REFERENCES `soluz`.`Questao` (`Codigo_Questao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `soluz`.`Discursiva` (
  `Codigo_Discursiva` INT NOT NULL AUTO_INCREMENT,
  `Resposta` TEXT NOT NULL,
  `Alunos_Matricula` VARCHAR(15) NOT NULL,
  `Questao_Codigo` INT NOT NULL,
  `Correta` INT NULL,
  PRIMARY KEY (`Codigo_Discursiva`),
  INDEX `fk_Discursiva_Alunos1_idx` (`Alunos_Matricula` ASC),
  INDEX `fk_Discursiva_Questao1_idx` (`Questao_Codigo` ASC),
  CONSTRAINT `fk_Discursiva_Alunos1`
    FOREIGN KEY (`Alunos_Matricula`)
    REFERENCES `soluz`.`Alunos` (`Matricula`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Discursiva_Questao1`
    FOREIGN KEY (`Questao_Codigo`)
    REFERENCES `soluz`.`Questao` (`Codigo_Questao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
select * from professores;
select * from alunos;
delete from serie where Descricao = 'Biologia';
select * from serie;
delete from serie where Codigo_Serie = 2;


CREATE TABLE IF NOT EXISTS `soluz`.`Resposta_Alternativa` (
  `Codigo_Resposta` INT NOT NULL AUTO_INCREMENT,
  `Alternativa_Alternativa_Codigo` INT NOT NULL,
  `Alunos_Matricula` VARCHAR(15) NOT NULL,
  `Resposta` TINYINT NOT NULL,
  PRIMARY KEY (`Codigo_Resposta`),
  UNIQUE INDEX `idResposta_UNIQUE` (`Codigo_Resposta` ASC),
  INDEX `fk_Resposta_Alternativa1_idx` (`Alternativa_Alternativa_Codigo` ASC),
  INDEX `fk_Resposta_Alunos1_idx` (`Alunos_Matricula` ASC),
  CONSTRAINT `fk_Resposta_Alternativa1`
    FOREIGN KEY (`Alternativa_Alternativa_Codigo`)
    REFERENCES `soluz`.`Alternativa` (`Codigo_Alternativa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Resposta_Alunos1`
    FOREIGN KEY (`Alunos_Matricula`)
    REFERENCES `soluz`.`Alunos` (`Matricula`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;





INSERT INTO `Alunos` (`Matricula`, `Email`, `Senha`, `Nome`, `Data_Nascimento`, `Ultimo_Login`) VALUES

('0001', 'aguydiovana@gmail.com', '65d8c8050a8218df8f057d826af01e332544045a','ÁGUEDA DIOVANA DE SOUZA NETTO','2003-03-17','2019-03-12 16:22:32'),# USER: 0001 // SENHA: netto

('0002', 'lanna.roeder@gmail.com', 'b059d286d11b6128637d2efaddf4937e0c56d6b4','ALANA ROEDER','2003-03-17','2019-03-12 16:22:32'),# USER: 0002 // SENHA: roeder

('0003', 'aline.cl.stock@gmail.com', 'ed487e1e87c675af89db011b2903f20f99b11c7d','ALINE CAROL STOCK','2003-03-17','2019-03-12 16:22:32'),# USER: 0003 // SENHA: stock

('0004', 'arthur.fuechter.schweder@gmail.com', '0f1eed1bc1099b9f64c06bc806cbce430cf27969','ARTHUR FUECHTER SCHWEDER','2003-03-17','2019-03-12 16:22:32'),# USER: 0004 // SENHA: schweder

('0005', '', 'bd24767f376d72d0efa191538d094c6bd3a80eab','ÁRTHUR PAULO MATTEUSSI','2003-03-17','2019-03-12 16:22:32'),# USER: 0005 // SENHA: matteussi

('0006', 'augusto.teixeira8579@gmail.com', '017d9b49ed2255dda4fde229135f24c65747af0d','AUGUSTO LEHMKUHL TEIXEIRA','2003-03-17','2019-03-12 16:22:32'),# USER: 0006 // SENHA: teixeira

('0007', '', 'bd24767f376d72d0efa191538d094c6bd3a80eab','BARBARA ELLEN DE LARA','2003-03-17','2019-03-12 16:22:32'),# USER: 0007 // SENHA: matteussi

('0008', '', 'bd24767f376d72d0efa191538d094c6bd3a80eab','ÁRTHUR PAULO MATTEUSSI','2003-03-17','2019-03-12 16:22:32'),# USER: 0008 // SENHA: matteussi

('0009', '', 'bd24767f376d72d0efa191538d094c6bd3a80eab','ÁRTHUR PAULO MATTEUSSI','2003-03-17','2019-03-12 16:22:32');# USER: 0009 // SENHA: matteussi

SELECT * FROM professores_has_disciplina;


SELECT * FROM avaliacoes;

SELECT d.Codigo_Disciplina as 'ID Disciplina', d.Nome as 'Disciplina', a.Codigo_Avaliacao as 'ID Avaliação', a.Conteudo, a.Data_Inicio, a.Data_Fim, a.Peso, a.Embaralhar
FROM avaliacoes a, disciplinas d
WHERE d.Codigo_Disciplina = a.Disciplina_Codigo_Disciplina
AND d.Codigo_Disciplina = 1;

SELECT * FROM Tipo;
SELECT * FROM Questao;
SELECT * FROM Avaliacoes;
SELECT * FROM Questoes_has_Avaliacoes;
INSERT INTO `Questao` (`Enunciado`, `Tipo_Codigo`) VALUES ('Explique o que é PHP',1);
INSERT INTO `Questoes_has_Avaliacoes` (`Questoes_Codigo_Questao`, `Avaliacoes_Codigo_Avaliacao`) VALUES (2,2);

SELECT A.Codigo_Avaliacao, A.Conteudo, Q.Codigo_Questao, Q.Enunciado, Q.Texto, T.Descricao as 'Tipo'
FROM Questao Q, Questoes_has_Avaliacoes QA, Avaliacoes A, Tipo T
WHERE QA.Questoes_Codigo_Questao = Q.Codigo_Questao
AND QA.Avaliacoes_Codigo_Avaliacao = A.Codigo_Avaliacao
AND Q.Tipo_Codigo = T.Codigo_Tipo
AND QA.Avaliacoes_Codigo_Avaliacao = 2;

SELECT * FROM Disciplinas;

SELECT D.Codigo_Disciplina, D.Nome
FROM Professores P, Disciplinas D, Professores_has_Disciplina PD
WHERE PD.Professores_Matricula = P.matricula
AND PD.Disciplina_Codigo_Disciplina = D.Codigo_Disciplina
AND P.Matricula = '2031';

select * FROM Questao;

SELECT * FROM Disciplinas;

SELECT * FROM Avaliacoes WHERE Codigo_Avaliacao = 1; 


INSERT INTO Questao (`Enunciado`, `Tipo_Codigo`) VALUES
('O que é PHP?',1)
,('Onde PHP é mais usado?',1)
,('Qual dos seguintes trechos de código são de PHP?',2)
,('Marque a alternativa INCORRETA sobre o PHP.',2)
,('Marque os trechos de código que NÃO são de PHP',3);

SELECT * FROM Questao;

INSERT INTO Alternativa (`Descricao`,`Correta`,`Questao_Codigo`) VALUES
('if num = 1: print("hello, your number is "+ num)',0,3)
,('$var = 1; $var++; echo $var;',1,3)
,('cout << "trust me am php code";',0,3);
INSERT INTO Alternativa (`Descricao`,`Correta`,`Questao_Codigo`) VALUES
('O PHP é uma linguagem de programação',0,4)
,('O PHP funciona do lado do cliente',1,4)
,('No PHP as variáveis são declaradas com $',0,4);
INSERT INTO Alternativa (`Descricao`,`Correta`,`Questao_Codigo`) VALUES
('function return_number(num){ return self.num }',1,5)
,('isphp = cin >> "is this a php code? >>; if (isphp) { cout << "yes, you are right"; }',1,5)
,('object.attribute = self.itself(self.relation){self.relation(self.itself = self.self)}',1,5)
,('um = leia("quanto é 1+1"); if(um != 2) { escreva("errou")}',1,5)
,('$php = false;',0,5);

INSERT INTO Questoes_has_Avaliacoes (`Questoes_Codigo_Questao`, `Avaliacoes_Codigo_Avaliacao`) VALUES
(1,1)
,(2,1)
,(3,1)
,(4,1)
,(5,1);

SELECT * FROM Serie;
INSERT INTO Serie (Descricao) VALUES ('3INFO');

SELECT * FROM Disciplinas;
INSERT INTO Disciplinas (Nome, Serie_Codigo_Serie) VALUES ('Banco de Dados', 1), ('Redes de Computadores', 1);

SELECT * FROM Avaliacoes;
INSERT INTO Avaliacoes (Conteudo, Disciplina_Codigo_Disciplina, Data_Inicio, Data_Fim, Embaralhar) VALUES
('MySQL Básico', 2, '2019-03-10 00:00:00', '2019-03-20 00:00:00', 0),
('MySQL Intermediário', 2, '2019-05-10 00:00:00', '2019-05-20 00:00:00', 1),
('MySQL Avançado', 2, '2019-07-10 00:00:00', '2019-07-20 00:00:00', 1);
INSERT INTO Avaliacoes (Conteudo, Disciplina_Codigo_Disciplina, Data_Inicio, Data_Fim, Embaralhar) VALUES
('O que são redes', 3, '2019-03-10 00:00:00', '2019-03-20 00:00:00', 1),
('Modelo OSI', 3, '2019-05-10 00:00:00', '2019-05-20 00:00:00', 0),
('IP e máscara de sub-rede', 3, '2019-07-10 00:00:00', '2019-07-20 00:00:00', 1);

SELECT * FROM Tipo;
SELECT * FROM Questao;
SELECT * FROM Avaliacoes;
SELECT * FROM Questoes_has_Avaliacoes;
INSERT INTO Questao (Enunciado, Tipo_Codigo) VALUES
('O que é MySQL?', 1),
('Quais dos seguintes comandos são de MySQL?', 3),
('Assinale a alternativa falsa sobre o MySQL', 2);
INSERT INTO Questoes_has_Avaliacoes (Questoes_Codigo_Questao, Avaliacoes_Codigo_Avaliacao) VALUES
(6, 2),
(7, 2),
(8, 2);

SELECT * FROM Questoes_has_Avaliacoes WHERE Avaliacoes_Codigo_Avaliacao = 2;
SELECT * FROM Questao;

SELECT * FROM Alternativa;
INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES
('select.tabela("nome, campo, idade");',0,7),
('INSERT INTO tabela (campo1, nome) VALUES (1), ("jondgr");',1,7),
('SELECT * FROM tabela;',1,7),
('tabela->SELECT(*) FROM WHERE value = 2;',0,7);
INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES
('o MySQL é um sistema gerenciador de banco de dados',0,8),
('no mySQL informações são armazenadas em tabelas',0,8),
('o MySQL é utilizado por meio de um site',1,8);


INSERT INTO Questao (Enunciado, Tipo_Codigo) VALUES
('Para que servem os comandos count() e avg()?', 1),
('Marque os comandos SQL que não dariam certo', 3),
('Qual o comando SQL usado para agrupar?', 2);
SELECT * FROM Questao;
SELECT * FROM Questoes_has_Avaliacoes;
SELECT * FROM Avaliacoes;
INSERT INTO Questoes_has_Avaliacoes (Questoes_Codigo_Questao, Avaliacoes_Codigo_Avaliacao) VALUES
(9, 3),
(10, 3),
(11, 3);
SELECT * FROM Alternativa;
INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES
('SELECT (SELECT numero FROM numero) WHERE numero = 10;', 1, 10),
('UPDATE numero = 5;', 1, 10),
('SELECT nome, idade FROM tabela WHERE nome = "jonsnerg";', 0, 10),
('INSERT numero = 5 INTO tabela;', 1, 10);
INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES
('SET GROUP campo;', 0, 11),
('GROUPY BY campo;', 1, 11),
('ORDER BY GROUP grupo;', 0, 11),
('SELECT GROUP FROM tabela;', 0, 11);

INSERT INTO Questao (Enunciado, Tipo_Codigo) VALUES
('O que são funções armazenadas?', 1),
('Marque as respostas corretas para 2+2', 3);
SELECT * FROM Questao;
SELECT * FROM Questoes_has_Avaliacoes;
SELECT * FROM Avaliacoes;
INSERT INTO Questoes_has_Avaliacoes (Questoes_Codigo_Questao, Avaliacoes_Codigo_Avaliacao) VALUES
(12, 4),
(13, 4);
INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES
('(2+4-2)^2', 0, 13),
('2^2', 1, 13),
('4', 1, 13),
('4-2+8-6', 1, 13);

UPDATE Avaliacoes SET Data_Fim = '2028-03-20 00:00:00' WHERE Codigo_Avaliacao > 0;
SELECT * FROM Avaliacoes;

INSERT INTO Professores_has_Disciplina (Professores_Matricula, Disciplina_Codigo_Disciplina) VALUES ('2017305998', 2), ('2017305998', 3);
SELECT * FROM Disciplina_has_Alunos;
SELECT * FROM Alunos;
SELECT * FROM Disciplinas;
INSERT INTO Disciplina_has_Alunos (Disciplina_Codigo_Disciplina, Alunos_Matricula) VALUES
(2,201701),
(2,201717),
(2,201771),
(3,201701),
(3,201717),
(3,201771);

SELECT * FROM Questao;
UPDATE Avaliacoes SET Data_Fim = '2018-03-20 00:00:00' WHERE Codigo_Avaliacao > 0;

SELECT Conteudo, Data_Fim FROM Avaliacoes WHERE Disciplina_Codigo_Disciplina = 2 ORDER BY Codigo_Avaliacao;

SELECT * FROM Alunos;

SELECT DA.Alunos_Matricula, A.Nome FROM Disciplina_has_Alunos DA, Alunos A WHERE DA.Disciplina_Codigo_Disciplina = 1 AND DA.Alunos_Matricula = A.Matricula ORDER BY DA.Alunos_Matricula;

SELECT * FROM Questao;
INSERT INTO Questao VALUES (Enunciado, Texto) VALUES ();

SELECT * FROM Avaliacoes;

SELECT * FROM Questao;
DELETE FROM Questao WHERE Codigo_Questao > 13;
SELECT * FROM Questoes_has_Avaliacoes;
DELETE FROM Questoes_has_Avaliacoes WHERE Questoes_Codigo_Questao = 33 AND Avaliacoes_Codigo_Avaliacao = 9;
