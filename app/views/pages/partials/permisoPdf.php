<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>

    <div class="permiso-header">
        <span class="permiso-titulo">PERMISO DE CIRCULACIÓN POR LA CIUDAD DE SAN CARLOS DE BARILOCHE</span>
        <span><?= $datos['id_permiso'] ?></span>
    </div>

    <p>
        <span class="bold"><?= $datos['tipo'] ?> / <?= $datos['arribo_salida'] ?></span>
        &nbsp;- Fecha validez: <span class="bold"><?= $datos['fecha_reserva'] ?></span><br>
        Servicio: <span class="bold"><?= $datos['empresa'] ?></span>
        &nbsp;- Dominio: <span class="bold"><?= $datos['dominio'] ?></span>
        &nbsp;- Interno: <span class="bold"><?= $datos['interno'] ?></span>
        &nbsp;- Pasajeros: <span class="bold"><?= $datos['pasajeros'] ?></span>
    </p>

    <?php if ($datos['tipo'] === 'charter'): ?>
        <p class="small bold">
            Deberá presentarse en el baño químico sin pasajeros de 08:00 a 22:00 hs
        </p>
    <?php endif; ?>

    <p>
        Observaciones: <span class="bold"><?= htmlspecialchars($datos['observacion']) ?></span>
    </p>
    <p>
        Recorrido: <?= htmlspecialchars($datos['calles_recorrido']) ?>
        <?php if ($datos['tipo'] === 'linea'): ?>
            - horario derecho de dársena: {hora_ocupa_darsena}
        <?php endif; ?>
    </p>

    <table>
        <thead>
            <tr>
                <th>CALLE</th>
                <th>PARADA</th>
                <th>HORA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos['paradas'] as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['calle']) ?></td>
                    <td>
                        <?= htmlspecialchars($fila['parada']) ?>
                        <?php if (!empty($fila['hotel'])): ?>
                            - <?= htmlspecialchars($fila['hotel']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($fila['horario']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table>
        <tr>
            <td class="text-center small bold">ABONO DERECHO DE DÁRSENA PARA <?= $datos['tipo'] === 'charter' ? 'EL INGRESO' : 'EL DESCENSO DE PAX' ?></td>
            <td class="text-center small bold">ABONO DERECHO DE DESCARGA DE BAÑO QUÍMICO</td>
            <td class="text-center small bold">ABONO DERECHO DE DÁRSENA PARA <?= $datos['tipo'] === 'charter' ? 'LA SALIDA' : 'EL ASCENSO DE PAX' ?></td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="signature-box">
                ......................<br>
                FIRMA DEL CONDUCTOR<br>
                <?= htmlspecialchars($datos['nombre_chofer']) ?> <?= htmlspecialchars($datos['apellido_chofer']) ?><br>
                DNI: <?= htmlspecialchars($datos['dni_chofer']) ?>
            </td>
            <td></td>
            <td class="signature-box">
                <?= htmlspecialchars($datos['usuario_nombre']) ?> <?= htmlspecialchars($datos['usuario_apellido']) ?><br>
                <?= htmlspecialchars($datos['usuario_cargo']) ?>, <?= htmlspecialchars($datos['usuario_sector']) ?><br>
                SAN CARLOS DE BARILOCHE
            </td>
        </tr>
    </table>

    <p class="notice">
        EL ITINERARIO AUTORIZADO HA SIDO SOLICITADO POR EL CONDUCTOR DE LA EMPRESA SIENDO ESTE RESPONSABLE SOLIDARIO DE LA MISMA A ESTOS EFECTOS. SUJETO A INFRACCIÓN EN CASO DE INCUMPLIR EL MISMO. EL ASCENSO Y DESCENSO DE PASAJEROS DEBE EFECTUARSE CON EL MOTOR APAGADO, POR ORDENANZA MUNICIPAL 808-CM-1997.<br>
        EL PRESENTE PERMISO DE CIRCULACIÓN DEBERÁ SER EXHIBIDO CADA VEZ QUE LE SEA REQUERIDO POR LA AUTORIDAD COMPETENTE (PROVINCIAL, MUNICIPAL O POLICIAL).
    </p>

    <p class="notice">
        LA UNIDAD NO ESTÁ AUTORIZADA A ESTACIONARSE SIN REALIZAR ACTIVIDAD Y/O PERNOCTAR EN LA VÍA PÚBLICA.<br>
        POR LEY PROVINCIAL N° 651 Y ORD. MUNICIPAL 500-CM-90 Y 506-CM-90 NO SE AUTORIZA LA CIRCULACIÓN DE UNIDADES DE MÁS DE 10 MTS. DE LARGO PARA LA REALIZACIÓN DE EXCURSIONES A LOS CIRCUITOS TURÍSTICOS: CIRCUITO CHICO, CERRO CATEDRAL, CERRO OTTO, PUERTO PAÑUELO Y TODAS LAS ALTERNATIVAS RELACIONADAS.
    </p>

    <hr>

</body>
</html>
