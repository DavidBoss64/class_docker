-- Base de datos de ejemplo para la masterclass de Docker
CREATE DATABASE IF NOT EXISTS inscripciones
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE inscripciones;

CREATE TABLE IF NOT EXISTS alumnos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  curso      VARCHAR(100) NOT NULL,
  creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO alumnos (nombre, email, curso) VALUES
  ('Ana Torres',   'ana@correo.com',   'Docker desde cero'),
  ('Luis Ramos',   'luis@correo.com',  'Laravel profesional'),
  ('María Quispe', 'maria@correo.com', 'Python para todos');
