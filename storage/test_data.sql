-- =============================================
-- DATOS MASIVOS DE PRUEBA - Sistema TERRANOVA v2
-- Volumen: ~150,000 registros simulando producción
-- FK consistentes: CLIENTE.ID_USER = RESIDENCIAL.ID_USER
-- Ejecutar después de lotificadora_scripts_postgres.sql
-- =============================================

-- 0. CONFIG: 6 usuarios, 1000 clientes por usuario, 10 residenciales por usuario
--    Password de todos = hash de 'password' (mismo que Administrador)

-- 1. USERS (6 usuarios: admin + 5 operadores)
--    Ya insertados por lotificadora_scripts_postgres.sql con la misma password.
--    IDs: 1=Administrador, 2=Op1, 3=Op2, 4=Op3, 5=Op4, 6=Op5

-- 2. RESIDENCIALES (10 por usuario = 60)
--    Distribución: IDs 1,7,13,...,55 → User 1; IDs 2,8,14,...,56 → User 2; etc.
INSERT INTO RESIDENCIALES (NOMBRE, DESCRIPCION, ID_USER)
SELECT
    n,
    'Residencial ' || n,
    1 + ((ROW_NUMBER() OVER ()) % 6)
FROM (VALUES
    ('Los Pinos'), ('Villas del Sol'), ('Bosque Real'),
    ('Altos del Valle'), ('Pradera Verde'), ('Monte Claro'),
    ('La Colina'), ('El Encanto'), ('San Jerónimo'), ('Valle Alto'),
    ('Santa Clara'), ('Los Álamos'), ('Rincón Bonito'), ('Vista Hermosa'),
    ('El Manantial'), ('Nueva Esperanza'), ('Bella Vista'), ('Los Olivos'),
    ('Jardines del Sur'), ('Puerta de Hierro'), ('Lomas Altas'), ('El Cortijo'),
    ('Las Fuentes'), ('Los Cipreses'), ('Cerro Gordo'), ('El Prado'),
    ('San Miguel'), ('La Cima'), ('Villahermosa'), ('Real del Monte'),
    ('Los Fresnos'), ('Campestre'), ('Valle Dorado'), ('Santa Fe'),
    ('La Herradura'), ('Los Sauces'), ('El Mirador'), ('San Nicolás'),
    ('Los Cedros'), ('San Antonio'), ('Las Palmas'), ('Los Encinos'),
    ('Rincón Colonial'), ('Los Pinos II'), ('Los Girasoles'), ('Santa Mónica'),
    ('Palmares'), ('Los Robles'), ('Valle Escondido'), ('Buenaventura'),
    ('Vista Real'), ('Las Acacias'), ('Portal del Sol'), ('Los Molinos'),
    ('Campo Real'), ('Las Brisas'), ('Paseo Alto'), ('La Floresta'),
    ('Montebello'), ('San Eduardo'), ('Los Laureles'), ('Valle Real')
) AS t(n)
WHERE NOT EXISTS (SELECT 1 FROM RESIDENCIALES WHERE NOMBRE = t.n);

-- 3. BLOQUES_RESIDENCIALES (60 × 5 = 300) — bloques A,B,C,D,E para cada residencial
--    Ya creados en la línea siguiente (solo si no existen)
INSERT INTO BLOQUES_RESIDENCIALES (ID_BLOQUE, ID_RESIDENCIAL)
SELECT B.ID, R.ID
FROM BLOQUES B, RESIDENCIALES R
WHERE B.NOMBRE IN ('A','B','C','D','E')
  AND NOT EXISTS (SELECT 1 FROM BLOQUES_RESIDENCIALES BR WHERE BR.ID_BLOQUE = B.ID AND BR.ID_RESIDENCIAL = R.ID);

-- 4. LOTES (100 por bloque_residencial = 30,000)
--    FK: ID_BLOQUE_RESIDENCIAL → BLOQUES_RESIDENCIALES.ID
DO $$
DECLARE
    brec RECORD;
    i INTEGER;
    lote_name TEXT;
    base_precio NUMERIC;
    v_area NUMERIC;
BEGIN
    FOR brec IN SELECT BRE.ID, B.NOMBRE AS BLOQUE, R.ID AS RESIDENCIAL_ID
              FROM BLOQUES_RESIDENCIALES BRE
              JOIN BLOQUES B ON BRE.ID_BLOQUE = B.ID
              JOIN RESIDENCIALES R ON BRE.ID_RESIDENCIAL = R.ID
    LOOP
        FOR i IN 1..100 LOOP
            lote_name := 'L-' || i;
            base_precio := 150000 + (brec.RESIDENCIAL_ID * 25000) + (i * 400);
            v_area := 150 + (i * 3);
            INSERT INTO LOTES (NOMBRE, LOTE, AREA, NORTE, SUR, ESTE, OESTE, PRECIO, ANIOS_FINANCIAMIENTO, ID_BLOQUE_RESIDENCIAL)
            SELECT lote_name, i, v_area,
                   round((v_area / 20)::numeric, 2), round((v_area / 20)::numeric, 2),
                   round((v_area / 20)::numeric, 2), round((v_area / 20)::numeric, 2),
                   base_precio, 15 + (i % 15), brec.ID
            WHERE NOT EXISTS (SELECT 1 FROM LOTES L WHERE L.ID_BLOQUE_RESIDENCIAL = brec.ID AND L.LOTE = i);
        END LOOP;
    END LOOP;
END $$;

-- 5. CLIENTES MASIVOS (6000 = 1000 por cada uno de los 6 usuarios)
--    Agrupados en bloques contiguos para que FK coincidan con residenciales.
--    User 1: IDs 1-1000, User 2: IDs 1001-2000, ..., User 6: IDs 5001-6000
INSERT INTO CLIENTES (PRIMER_NOMBRE, SEGUNDO_NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO, IDENTIDAD, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2, CORREO_ELECTRONICO, DIRECCION, ID_USER)
SELECT
    (ARRAY['Juan','María','Carlos','Ana','Pedro','Sofía','Luis','Carmen','José','Rosa',
            'Miguel','Elena','Fernando','Laura','Ricardo','Diana','Andrés','Patricia','Javier','Marta',
            'Daniel','Silvia','Roberto','Verónica','Alberto','Gabriela','Manuel','Isabel','Alejandro','Adriana',
            'Jorge','Teresa','Vicente','Lorena','Enrique','Claudia','Raúl','Liliana','Héctor','Mónica',
            'Arturo','Natalia','Sergio','Alicia','Francisco','Beatriz','Guillermo','Ruth','Oscar','Ángela',
            'David','Eva','Iván','Sara','Pablo','Olga','Mario','Yolanda','Ramiro','Brenda',
            'Fabián','Ximena','Gustavo','Marina','Hugo','Cecilia','Igor','Pilar','Joel','Aldana',
            'Kevin','Tamara','Leonel','Violeta','Marvin','Milenka','Nelson','Zulema','Omar','Samantha',
            'Plutarco','Roxana','Quique','Nadia','Saúl','Mara','Tobías','Leticia','Ulises','Karen',
            'Waldo','Yadira','Xavier','Paulina','Zacarías','Ninfa','Adán','Xiomara','Benito','Fabiola'])[1 + (i % 100)],
    CASE WHEN i % 4 = 0 THEN (ARRAY['Alberto','Elena','Antonio','Lucía','Fernando','Isabel','Jorge','Mariana','Felipe','Raquel'])[1 + (i % 10)] ELSE NULL END,
    (ARRAY['Pérez','García','Hernández','Torres','Flores','Reyes','Castillo','Morales','López','Martínez',
            'Cruz','Ramírez','Mendoza','Vargas','Ortiz','Díaz','Castro','Romero','Álvarez','Gutiérrez',
            'Rivas','Soto','Peña','Navarro','Delgado','Guerrero','Medina','Aguilar','Herrera','Vega',
            'Campos','Rivera','Miranda','Cortés','Santos','Vázquez','Jiménez','Ruiz','Ramos','Moreno',
            'Paredes','Acosta','Molina','Chávez','Salazar','Núñez','Espinoza','Padilla','Rojas','Ibarra',
            'Méndez','Fuentes','Carrillo','Contreras','Figueroa','Solís','Trujillo','Córdova','Valencia','Bautista',
            'Cuevas','Rosales','Espinosa','Mejía','Bravo','Villanueva','Gallegos','Tovar','Pacheco','Ceballos',
            'Lemus','Alvarado','Carranza','Bermúdez','Zavala','Escobar','Tapia','Maya','Barrientos','Marroquín',
            'Alfaro','Zelaya','Arévalo','Cordón','Escalante','Lemus','Monzón','Paniagua','Rabanales','Samayoa',
            'Tejada','Ureña','Vásquez','Wong','Xirum','Yac','Zepeda','Arriaga','Burgos','Calderón'])[1 + (i % 100)],
    (ARRAY['López','Martínez','Cruz','Ramírez','Mendoza','Vargas','Ortiz','Díaz','Castro','Romero'])[1 + (i % 10)],
    LPAD(i::TEXT, 13, '0'),
    '9999-' || LPAD(i::TEXT, 4, '0'),
    CASE WHEN i % 5 = 0 THEN '8888-' || LPAD(i::TEXT, 4, '0') ELSE NULL END,
    CASE WHEN i % 3 = 0 THEN 'cliente' || i || '@email.com' ELSE NULL END,
    CASE (i % 20)
        WHEN 0 THEN 'Col. Kennedy, Bloque ' || (i % 10) || ', Casa ' || i
        WHEN 1 THEN 'Barrio El Centro, ' || (i % 10) || 'ra Calle, Casa ' || i
        WHEN 2 THEN 'Residencial Las Colinas, Casa ' || i
        WHEN 3 THEN 'Col. La Granja, Calle ' || (i % 10) || ', Casa ' || i
        WHEN 4 THEN 'Urbanización El Valle, Casa ' || i
        WHEN 5 THEN 'Col. Palmira, Av. ' || (i % 10) || ', Casa ' || i
        WHEN 6 THEN 'Barrio La Joya, Casa ' || i
        WHEN 7 THEN 'Col. Miraflores, Calle ' || (i % 10) || ', Casa ' || i
        WHEN 8 THEN 'Residencial Las Fuentes, Casa ' || i
        WHEN 9 THEN 'Col. San Miguel, Calle ' || (i % 10) || ', Casa ' || i
        WHEN 10 THEN 'Barrio El Calvario, Casa ' || i
        WHEN 11 THEN 'Urbanización Los Álamos, Casa ' || i
        WHEN 12 THEN 'Col. Santa Clara, Casa ' || i
        WHEN 13 THEN 'Residencial El Manantial, Casa ' || i
        WHEN 14 THEN 'Col. Bella Vista, Calle ' || (i % 10) || ', Casa ' || i
        WHEN 15 THEN 'Barrio San José, Casa ' || i
        WHEN 16 THEN 'Col. Centro América, Casa ' || i
        WHEN 17 THEN 'Residencial Las Mercedes, Casa ' || i
        WHEN 18 THEN 'Urbanización Los Pinos, Casa ' || i
        ELSE 'Dirección General #' || i || ', Ciudad'
    END,
    -- ASIGNAR USUARIO: bloques contiguos de 1000 clientes cada uno
    1 + ((i - 1) / 1000)
FROM generate_series(1, 6000) AS i
WHERE NOT EXISTS (SELECT 1 FROM CLIENTES C WHERE C.IDENTIDAD = LPAD(i::TEXT, 13, '0'));

-- 6. REFERENCIAS MASIVAS (3 por cliente = 18,000)
INSERT INTO REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE)
SELECT
    (ARRAY['Juan','María','Carlos','Ana'])[1 + (C.ID % 4)] || ' ' || C.PRIMER_APELLIDO || ' (Ref 1)',
    '7000-' || LPAD(C.ID::TEXT, 4, '0'),
    'Dir Ref 1 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM CLIENTES C
WHERE NOT EXISTS (SELECT 1 FROM REFERENCIAS R WHERE R.CONTACTO_TELEFONICO = '7000-' || LPAD(C.ID::TEXT, 4, '0'));

INSERT INTO REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE)
SELECT
    (ARRAY['Pedro','Ana','Luis','Sofía'])[1 + (C.ID % 4)] || ' ' || C.SEGUNDO_APELLIDO || ' (Ref 2)',
    '7001-' || LPAD(C.ID::TEXT, 4, '0'),
    'Dir Ref 2 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM CLIENTES C
WHERE NOT EXISTS (SELECT 1 FROM REFERENCIAS R WHERE R.CONTACTO_TELEFONICO = '7001-' || LPAD(C.ID::TEXT, 4, '0'));

INSERT INTO REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE)
SELECT
    (ARRAY['José','Carmen','Luis','Rosa'])[1 + (C.ID % 4)] || ' ' || C.PRIMER_APELLIDO || ' (Ref 3)',
    '7002-' || LPAD(C.ID::TEXT, 4, '0'),
    'Dir Ref 3 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM CLIENTES C
WHERE NOT EXISTS (SELECT 1 FROM REFERENCIAS R WHERE R.CONTACTO_TELEFONICO = '7002-' || LPAD(C.ID::TEXT, 4, '0'));

-- 7. BENEFICIARIOS MASIVOS (2 por cliente = 12,000)
INSERT INTO BENEFICIARIOS (NOMBRE_COMPLETO, IDENTIDAD, PARENTEZCO, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2, CORREO_ELECTRONICO, DIRECCION, ID_CLIENTE)
SELECT
    'Benef 1 del Cliente ' || C.ID,
    LPAD((10000 + C.ID)::TEXT, 13, '0'),
    (ARRAY['Esposo(a)','Hijo(a)','Padre/Madre','Hermano(a)','Tío(a)','Abuelo(a)','Sobrino(a)','Primo(a)'])[1 + (C.ID % 8)],
    '6000-' || LPAD(C.ID::TEXT, 4, '0'),
    CASE WHEN C.ID % 3 = 0 THEN '6001-' || LPAD(C.ID::TEXT, 4, '0') ELSE NULL END,
    CASE WHEN C.ID % 2 = 0 THEN 'benef1_' || C.ID || '@email.com' ELSE NULL END,
    'Dir Benef 1 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM CLIENTES C
WHERE NOT EXISTS (SELECT 1 FROM BENEFICIARIOS B WHERE B.IDENTIDAD = LPAD((10000 + C.ID)::TEXT, 13, '0'));

INSERT INTO BENEFICIARIOS (NOMBRE_COMPLETO, IDENTIDAD, PARENTEZCO, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2, CORREO_ELECTRONICO, DIRECCION, ID_CLIENTE)
SELECT
    'Benef 2 del Cliente ' || C.ID,
    LPAD((20000 + C.ID)::TEXT, 13, '0'),
    (ARRAY['Esposo(a)','Hijo(a)','Padre/Madre','Hermano(a)','Tío(a)','Abuelo(a)','Sobrino(a)','Primo(a)'])[1 + ((C.ID + 3) % 8)],
    '6002-' || LPAD(C.ID::TEXT, 4, '0'),
    CASE WHEN C.ID % 4 = 0 THEN '6003-' || LPAD(C.ID::TEXT, 4, '0') ELSE NULL END,
    CASE WHEN C.ID % 3 = 0 THEN 'benef2_' || C.ID || '@email.com' ELSE NULL END,
    'Dir Benef 2 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM CLIENTES C
WHERE NOT EXISTS (SELECT 1 FROM BENEFICIARIOS B WHERE B.IDENTIDAD = LPAD((20000 + C.ID)::TEXT, 13, '0'));

-- 8. VENTAS (50 por residencial = 3000 total)
--    FK consistente: CLIENTE.ID_USER = RESIDENCIAL.ID_USER
--    Cada residencial recibe 25 Contado (Pagado) + 25 Crédito (Activo)
--    Usamos CLIENTES del mismo usuario que el RESIDENCIAL
--    User N: CLIENTES con IDs ((N-1)*1000 + 1) hasta (N*1000)
DO $$
DECLARE
    rrec RECORD;
    lote_ids BIGINT[];
    client_ids BIGINT[];
    idx INT;
    lote_id BIGINT;
    client_id BIGINT;
    lote_precio NUMERIC;
    venta_id BIGINT;
    total_intereses NUMERIC;
    total_pagar NUMERIC;
    cuota_mensual NUMERIC;
    anios INT;
    cuotas INT;
    tasa NUMERIC;
BEGIN
    -- Por cada residencial
    FOR rrec IN SELECT R.ID AS RID, R.ID_USER AS RUSER
                FROM RESIDENCIALES R
                WHERE R.DELETED_AT IS NULL
                ORDER BY R.ID
    LOOP
        -- Recolectar lotes disponibles de este residencial (excluir ya vendidos)
        SELECT ARRAY_AGG(L.ID ORDER BY L.ID)
        INTO lote_ids
        FROM LOTES L
        JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
        WHERE BR.ID_RESIDENCIAL = rrec.RID
          AND L.DELETED_AT IS NULL
          AND NOT EXISTS (SELECT 1 FROM LOTES_VENDIDOS LV WHERE LV.ID_LOTE = L.ID);

        -- Recolectar clientes del mismo usuario (solo IDs)
        SELECT ARRAY_AGG(C.ID ORDER BY C.ID)
        INTO client_ids
        FROM CLIENTES C
        WHERE C.ID_USER = rrec.RUSER
          AND C.DELETED_AT IS NULL;

        -- Si no hay suficientes lotes o clientes, saltar
        IF array_length(lote_ids, 1) IS NULL OR array_length(client_ids, 1) IS NULL THEN
            CONTINUE;
        END IF;

        -- 25 VENTAS Contado - Pagadas
        FOR idx IN 1..LEAST(25, array_length(lote_ids, 1)) LOOP
            lote_id := lote_ids[idx];
            client_id := client_ids[1 + ((idx - 1) % array_length(client_ids, 1))];

            SELECT PRECIO INTO lote_precio FROM LOTES WHERE ID = lote_id;

            INSERT INTO VENTAS (ID_CLIENTE, TIPO_PAGO, ESTADO, TOTAL_CONTADO,
                                ANIOS_FINANCIAMIENTO, TASA_INTERES, PRIMA, CUOTAS,
                                TOTAL_INTERESES, TOTAL_PAGAR, CUOTA_MENSUAL, DIA_COBRO_MES, FECHA_VENTA)
            VALUES (client_id,
                    (SELECT ID FROM CATALOGO_TIPO_PAGO WHERE NOMBRE = 'Contado'),
                    (SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Pagado'),
                    lote_precio,
                    0, 0, lote_precio, 0,
                    0, lote_precio, 0,
                    1,
                    CURRENT_DATE - ((idx % 365) * INTERVAL '1 day'))
            ON CONFLICT DO NOTHING;

            IF FOUND THEN
                venta_id := LASTVAL();
                INSERT INTO LOTES_VENDIDOS (ID_LOTE, ID_VENTA) VALUES (lote_id, venta_id) ON CONFLICT DO NOTHING;
            END IF;
        END LOOP;

        -- 25 VENTAS Crédito - Activas
        FOR idx IN 26..LEAST(50, array_length(lote_ids, 1)) LOOP
            lote_id := lote_ids[idx];
            client_id := client_ids[1 + ((idx - 1) % array_length(client_ids, 1))];

            SELECT PRECIO INTO lote_precio FROM LOTES WHERE ID = lote_id;

            anios := (ARRAY[5,10,15,20])[1 + (idx % 4)];
            cuotas := anios * 12;
            tasa := 6.0 + ((idx % 15) * 0.5);
            total_intereses := lote_precio * 0.85 * (tasa / 100) * anios;
            total_pagar := lote_precio + total_intereses;
            cuota_mensual := total_pagar / cuotas;

            INSERT INTO VENTAS (ID_CLIENTE, TIPO_PAGO, ESTADO, TOTAL_CONTADO,
                                ANIOS_FINANCIAMIENTO, TASA_INTERES, PRIMA, CUOTAS,
                                TOTAL_INTERESES, TOTAL_PAGAR, CUOTA_MENSUAL, DIA_COBRO_MES, FECHA_VENTA)
            VALUES (client_id,
                    (SELECT ID FROM CATALOGO_TIPO_PAGO WHERE NOMBRE = 'Financiado'),
                    (SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Activo'),
                    lote_precio,
                    anios, tasa, lote_precio * 0.15, cuotas,
                    total_intereses, total_pagar, cuota_mensual,
                    1 + (idx % 28),
                    CURRENT_DATE - ((idx % 730) * INTERVAL '1 day'))
            ON CONFLICT DO NOTHING;

            IF FOUND THEN
                venta_id := LASTVAL();
                INSERT INTO LOTES_VENDIDOS (ID_LOTE, ID_VENTA) VALUES (lote_id, venta_id) ON CONFLICT DO NOTHING;
            END IF;
        END LOOP;
    END LOOP;
END $$;

-- 9. FECHAS_COBROS MASIVAS (~60,000 registros para ventas Activo)
--    Solo para ventas con estado = Activo (Financiado)
--    Primeras 6 cuotas pagadas, el resto pendientes
DO $$
DECLARE
    vrec RECORD;
    i INTEGER;
    v_fecha_cobro DATE;
    v_fecha_pago DATE;
    v_monto NUMERIC;
    cuotas_por_venta INTEGER;
BEGIN
    FOR vrec IN SELECT VT.ID, VT.DIA_COBRO_MES, VT.CUOTA_MENSUAL, VT.FECHA_VENTA, VT.CUOTAS
              FROM VENTAS VT
              JOIN CATALOGO_ESTADO_VENTA EV ON VT.ESTADO = EV.ID
              WHERE EV.NOMBRE = 'Activo'
    LOOP
        cuotas_por_venta := LEAST(vrec.CUOTAS, 36);
        FOR i IN 1..cuotas_por_venta LOOP
            v_fecha_cobro := (vrec.FECHA_VENTA::DATE + (i * INTERVAL '1 month'))::DATE;
            BEGIN
                v_fecha_cobro := (EXTRACT(YEAR FROM v_fecha_cobro)::TEXT || '-' ||
                               LPAD(EXTRACT(MONTH FROM v_fecha_cobro)::TEXT, 2, '0') || '-' ||
                               LPAD(vrec.DIA_COBRO_MES::TEXT, 2, '0'))::DATE;
            EXCEPTION WHEN OTHERS THEN
                v_fecha_cobro := (EXTRACT(YEAR FROM v_fecha_cobro)::TEXT || '-' ||
                               LPAD(EXTRACT(MONTH FROM v_fecha_cobro)::TEXT, 2, '0') || '-' || '28')::DATE;
            END;

            IF i <= 6 THEN
                v_fecha_pago := v_fecha_cobro + ((i % 3) * INTERVAL '1 day');
                v_monto := vrec.CUOTA_MENSUAL;
            ELSE
                v_fecha_pago := NULL;
                v_monto := NULL;
            END IF;

            INSERT INTO FECHAS_COBROS (ID_VENTA, FECHA_COBRO, FECHA_PAGO, CANTIDAD_PAGO)
            SELECT vrec.ID, v_fecha_cobro, v_fecha_pago, v_monto
            WHERE NOT EXISTS (SELECT 1 FROM FECHAS_COBROS FC WHERE FC.ID_VENTA = vrec.ID AND FC.FECHA_COBRO = v_fecha_cobro);
        END LOOP;
    END LOOP;
END $$;

-- 10. SIMULAR ATRASOS (aleatorio: ~9% de las cuotas pagadas se marcan como atrasadas)
UPDATE FECHAS_COBROS SET
    CANTIDAD_PAGO = NULL,
    FECHA_PAGO = NULL
WHERE FECHA_COBRO < CURRENT_DATE
  AND FECHA_PAGO IS NOT NULL
  AND ID % 11 = 0;

-- 11. LOTES RESERVADOS (500)
UPDATE LOTES L SET
    ID_CLIENTE_RESERVAR = (
        SELECT C.ID FROM CLIENTES C
        JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
        WHERE C.ID_USER = (SELECT R.ID_USER FROM RESIDENCIALES R WHERE R.ID = BR.ID_RESIDENCIAL)
        ORDER BY C.ID
        LIMIT 1 OFFSET (L.ID % 1000)
    ),
    RESERVADO_HASTA = CURRENT_DATE + (15 + (L.ID % 30))
WHERE L.ID IN (
    SELECT L2.ID FROM LOTES L2
    WHERE L2.ID_CLIENTE_RESERVAR IS NULL
      AND L2.ID NOT IN (SELECT ID_LOTE FROM LOTES_VENDIDOS)
    ORDER BY L2.ID
    LIMIT 500
);

-- 12. LOTES_APARTADOS (500)
INSERT INTO LOTES_APARTADOS (ID_LOTE, ID_USER)
SELECT
    L.ID,
    1 + (L.ID % 6)
FROM LOTES L
WHERE L.ID NOT IN (SELECT ID_LOTE FROM LOTES_VENDIDOS)
  AND L.ID NOT IN (SELECT ID_LOTE FROM LOTES_APARTADOS)
ORDER BY L.ID
LIMIT 500;
