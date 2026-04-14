
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