
CREATE TABLE IF NOT EXISTS public.residenciales
(
    id serial,
    nombre text NOT NULL,
    descripcion text NOT NULL,
    id_user bigint NOT NULL,
	imagen text,
    created_at timestamp(0) without time zone DEFAULT now(),
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT residenciales_pkey PRIMARY KEY (id)
);
ALTER TABLE residenciales ADD CONSTRAINT residenciales_id_user_foreign FOREIGN KEY (id_user) REFERENCES users (id);

CREATE TABLE IF NOT EXISTS public.bloques
(
    id serial,
    nombre text NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now(),
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT bloques_pkey PRIMARY KEY (id)
);

INSERT INTO bloques (nombre) VALUES
('A'),('B'),('C'),('D'),('E'),('F'),('G'),
('H'),('I'),('J'),('K'),('L'),('M'),('N'),
('O'),('P'),('Q'),('R'),('S'),('T'),('U'),
('V'),('W'),('X'),('Y'),('Z');

CREATE TABLE IF NOT EXISTS public.bloques_residenciales
(
    id serial,
    id_bloque bigint NOT NULL,
    id_residencial bigint NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now(),
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT bloques_residenciales_pkey PRIMARY KEY (id)
);
ALTER TABLE bloques_residenciales ADD CONSTRAINT bloques_residenciales_id_bloque_foreign FOREIGN KEY (id_bloque) REFERENCES bloques (id);
ALTER TABLE bloques_residenciales ADD CONSTRAINT bloques_residenciales_id_residencial_foreign FOREIGN KEY (id_residencial) REFERENCES residenciales (id);
