-- =============================================
-- DATOS MASIVOS DE PRUEBA - Sistema TERRANOVA
-- Volumen: ~150,000 registros simulando producción
-- Ejecutar después de lotificadora_scripts_postgres.sql
-- =============================================

-- 1. USERS (6 usuarios: admin + 5 operadores)
-- contraseña para todos: password
INSERT INTO USERS (NAME, EMAIL, USERNAME, PASSWORD)
SELECT name, email, username, password FROM (VALUES
    ('Admin', 'admin@loti.com', 'admin', '$2y$10$jsPuTA62MG6/p/xC2J8I/OLJVCWd4nJsWzpItHNXse8NzG9rQqrx6'),
    ('Operador 1', 'op1@loti.com', 'op1', '$2y$10$jsPuTA62MG6/p/xC2J8I/OLJVCWd4nJsWzpItHNXse8NzG9rQqrx6'),
    ('Operador 2', 'op2@loti.com', 'op2', '$2y$10$jsPuTA62MG6/p/xC2J8I/OLJVCWd4nJsWzpItHNXse8NzG9rQqrx6'),
    ('Operador 3', 'op3@loti.com', 'op3', '$2y$10$jsPuTA62MG6/p/xC2J8I/OLJVCWd4nJsWzpItHNXse8NzG9rQqrx6'),
    ('Operador 4', 'op4@loti.com', 'op4', '$2y$10$jsPuTA62MG6/p/xC2J8I/OLJVCWd4nJsWzpItHNXse8NzG9rQqrx6'),
    ('Operador 5', 'op5@loti.com', 'op5', '$2y$10$jsPuTA62MG6/p/xC2J8I/OLJVCWd4nJsWzpItHNXse8NzG9rQqrx6')
) AS t(name, email, username, password)
WHERE NOT EXISTS (SELECT 1 FROM USERS WHERE USERNAME = t.username);

-- 2. RESIDENCIALES (60)
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

-- 3. BLOQUES_RESIDENCIALES (60 x 5 = 300)
INSERT INTO BLOQUES_RESIDENCIALES (ID_BLOQUE, ID_RESIDENCIAL)
SELECT B.ID, R.ID
FROM BLOQUES B, RESIDENCIALES R
WHERE B.NOMBRE IN ('A','B','C','D','E')
  AND NOT EXISTS (SELECT 1 FROM BLOQUES_RESIDENCIALES BR WHERE BR.ID_BLOQUE = B.ID AND BR.ID_RESIDENCIAL = R.ID);

-- 4. LOTES MASIVOS (30,000)
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
            lote_name := brec.BLOQUE || '-' || LPAD(i::TEXT, 3, '0');
            base_precio := 150000 + (brec.RESIDENCIAL_ID * 25000) + (i * 400);
            v_area := 150 + (i * 3);
            INSERT INTO LOTES (NOMBRE, LOTE, AREA, NORTE, SUR, ESTE, OESTE, PRECIO, ANIOS_FINANCIAMIENTO, ID_BLOQUE_RESIDENCIAL)
            SELECT lote_name, i, v_area,
                   round((v_area / 20)::numeric, 2), round((v_area / 20)::numeric, 2),
                   round((v_area / 20)::numeric, 2), round((v_area / 20)::numeric, 2),
                   base_precio, 15 + (i % 15), brec.ID
            WHERE NOT EXISTS (SELECT 1 FROM LOTES L WHERE L.NOMBRE = lote_name);
        END LOOP;
    END LOOP;
END $$;

-- 5. CLIENTES MASIVOS (5,000 distribuidos entre 6 usuarios)
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
    1 + (i % 6)
FROM generate_series(1, 5000) AS i
WHERE NOT EXISTS (SELECT 1 FROM CLIENTES C WHERE C.IDENTIDAD = LPAD(i::TEXT, 13, '0'));

-- 6. REFERENCIAS MASIVAS (3 por cliente = 15,000)
INSERT INTO REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE)
SELECT
    'Ref 1 - ' || C.NOMBRE || ' ' || C.APELLIDO,
    '7000-' || LPAD(C.ID::TEXT, 4, '0'),
    'Dir Ref 1 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM (SELECT ID, PRIMER_NOMBRE AS NOMBRE, PRIMER_APELLIDO AS APELLIDO FROM CLIENTES) C
WHERE NOT EXISTS (SELECT 1 FROM REFERENCIAS R WHERE R.CONTACTO_TELEFONICO = '7000-' || LPAD(C.ID::TEXT, 4, '0'))
  AND C.ID <= 5000;

INSERT INTO REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE)
SELECT
    'Ref 2 - ' || C.NOMBRE || ' ' || C.APELLIDO,
    '7001-' || LPAD(C.ID::TEXT, 4, '0'),
    'Dir Ref 2 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM (SELECT ID, PRIMER_NOMBRE AS NOMBRE, PRIMER_APELLIDO AS APELLIDO FROM CLIENTES) C
WHERE NOT EXISTS (SELECT 1 FROM REFERENCIAS R WHERE R.CONTACTO_TELEFONICO = '7001-' || LPAD(C.ID::TEXT, 4, '0'))
  AND C.ID <= 5000;

INSERT INTO REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE)
SELECT
    'Ref 3 - ' || C.NOMBRE || ' ' || C.APELLIDO,
    '7002-' || LPAD(C.ID::TEXT, 4, '0'),
    'Dir Ref 3 del Cliente ' || C.ID || ', Ciudad',
    C.ID
FROM (SELECT ID, PRIMER_NOMBRE AS NOMBRE, PRIMER_APELLIDO AS APELLIDO FROM CLIENTES) C
WHERE NOT EXISTS (SELECT 1 FROM REFERENCIAS R WHERE R.CONTACTO_TELEFONICO = '7002-' || LPAD(C.ID::TEXT, 4, '0'))
  AND C.ID <= 5000;

-- 7. BENEFICIARIOS MASIVOS (2 por cliente = 10,000)
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
WHERE NOT EXISTS (SELECT 1 FROM BENEFICIARIOS B WHERE B.IDENTIDAD = LPAD((10000 + C.ID)::TEXT, 13, '0'))
  AND C.ID <= 5000;

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
WHERE NOT EXISTS (SELECT 1 FROM BENEFICIARIOS B WHERE B.IDENTIDAD = LPAD((20000 + C.ID)::TEXT, 13, '0'))
  AND C.ID <= 5000;

-- 8. VENTAS MASIVAS (5,000)
-- 8a. Contado - Pagadas (2,500)
INSERT INTO VENTAS (ID_CLIENTE, TIPO_PAGO, ESTADO, TOTAL_CONTADO, ANIOS_FINANCIAMIENTO, TASA_INTERES, PRIMA, CUOTAS, TOTAL_INTERESES, TOTAL_PAGAR, CUOTA_MENSUAL, DIA_COBRO_MES, FECHA_VENTA)
SELECT
    (L.rn % 5000) + 1,
    TP.ID,
    EV.ID,
    L.PRECIO,
    0, 0, L.PRECIO, 0, 0, L.PRECIO, 0,
    1,
    CURRENT_DATE - ((L.rn % 1095) * INTERVAL '1 day')
FROM (
    SELECT ID, PRECIO, ROW_NUMBER() OVER (ORDER BY ID) AS rn
    FROM LOTES
    WHERE ID NOT IN (SELECT ID_LOTE FROM LOTES_VENDIDOS)
    ORDER BY ID
    LIMIT 2500
) L
CROSS JOIN CATALOGO_TIPO_PAGO TP
CROSS JOIN CATALOGO_ESTADO_VENTA EV
WHERE TP.NOMBRE = 'Contado' AND EV.NOMBRE = 'Pagado'
  AND NOT EXISTS (SELECT 1 FROM VENTAS V WHERE V.ID_CLIENTE = (L.rn % 5000) + 1 AND V.TOTAL_CONTADO = L.PRECIO AND V.TIPO_PAGO = TP.ID);

-- 8b. Crédito - Activas (2,500)
INSERT INTO VENTAS (ID_CLIENTE, TIPO_PAGO, ESTADO, TOTAL_CONTADO, ANIOS_FINANCIAMIENTO, TASA_INTERES, PRIMA, CUOTAS, TOTAL_INTERESES, TOTAL_PAGAR, CUOTA_MENSUAL, DIA_COBRO_MES, FECHA_VENTA)
SELECT
    ((L.rn + 2500) % 5000) + 1,
    TP.ID,
    EV.ID,
    L.PRECIO,
    (ARRAY[5,10,15,20,25,30])[1 + (L.rn % 6)],
    6.0 + ((L.rn % 20) * 0.25),
    L.PRECIO * 0.15,
    (ARRAY[60,120,180,240,300,360])[1 + (L.rn % 6)],
    L.PRECIO * 0.85 * (6.0 + ((L.rn % 20) * 0.25)) * (ARRAY[5,10,15,20,25,30])[1 + (L.rn % 6)] / 100,
    L.PRECIO + (L.PRECIO * 0.85 * (6.0 + ((L.rn % 20) * 0.25)) * (ARRAY[5,10,15,20,25,30])[1 + (L.rn % 6)] / 100),
    (L.PRECIO + (L.PRECIO * 0.85 * (6.0 + ((L.rn % 20) * 0.25)) * (ARRAY[5,10,15,20,25,30])[1 + (L.rn % 6)] / 100)) / (ARRAY[60,120,180,240,300,360])[1 + (L.rn % 6)],
    1 + (L.rn % 28),
    CURRENT_DATE - ((L.rn % 730) * INTERVAL '1 day')
FROM (
    SELECT ID, PRECIO, ROW_NUMBER() OVER (ORDER BY ID DESC) AS rn
    FROM LOTES
    WHERE ID NOT IN (SELECT ID_LOTE FROM LOTES_VENDIDOS)
    ORDER BY ID DESC
    LIMIT 2500
) L
CROSS JOIN CATALOGO_TIPO_PAGO TP
CROSS JOIN CATALOGO_ESTADO_VENTA EV
WHERE TP.NOMBRE = 'Financiado' AND EV.NOMBRE = 'Activo'
  AND NOT EXISTS (SELECT 1 FROM VENTAS V WHERE V.ID_CLIENTE = ((L.rn + 2500) % 5000) + 1 AND V.TIPO_PAGO = TP.ID);

-- 9. LOTES_VENDIDOS (vincular 5000 lotes a ventas)
INSERT INTO LOTES_VENDIDOS (ID_LOTE, ID_VENTA)
SELECT L.ID, V.ID
FROM (
    SELECT L2.ID, ROW_NUMBER() OVER (ORDER BY L2.ID) AS rn
    FROM LOTES L2
    WHERE NOT EXISTS (SELECT 1 FROM LOTES_VENDIDOS LV WHERE LV.ID_LOTE = L2.ID)
    ORDER BY L2.ID
    LIMIT 5000
) L
JOIN (SELECT V2.ID, ROW_NUMBER() OVER (ORDER BY V2.ID) AS rn FROM VENTAS V2) V ON L.rn = V.rn;

-- 10. LOTES RESERVADOS (500)
UPDATE LOTES L SET
    ID_CLIENTE_RESERVAR = (SELECT CL.ID FROM CLIENTES CL ORDER BY CL.ID LIMIT 1 OFFSET (L.ID % 5000)),
    RESERVADO_HASTA = CURRENT_DATE + (15 + (L.ID % 30))
WHERE L.ID IN (
    SELECT L2.ID FROM LOTES L2
    WHERE L2.ID_CLIENTE_RESERVAR IS NULL
      AND L2.ID NOT IN (SELECT ID_LOTE FROM LOTES_VENDIDOS)
    ORDER BY L2.ID
    LIMIT 500
);

-- 11. LOTES_APARTADOS (500)
INSERT INTO LOTES_APARTADOS (ID_LOTE, ID_USER)
SELECT
    L.ID,
    1 + (L.ID % 6)
FROM LOTES L
WHERE L.ID NOT IN (SELECT ID_LOTE FROM LOTES_VENDIDOS)
  AND L.ID NOT IN (SELECT ID_LOTE FROM LOTES_APARTADOS)
ORDER BY L.ID
LIMIT 500;

-- 12. FECHAS_COBROS MASIVAS (~60,000 registros)
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
                v_fecha_pago := v_fecha_cobro + ((i % 5) * INTERVAL '1 day');
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

-- 13. SIMULAR ATRASOS HISTÓRICOS (aleatorio)
UPDATE FECHAS_COBROS SET
    CANTIDAD_PAGO = NULL,
    FECHA_PAGO = NULL
WHERE FECHA_COBRO < CURRENT_DATE
  AND FECHA_PAGO IS NOT NULL
  AND ID % 11 = 0;
