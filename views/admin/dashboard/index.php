<main class="bloques">
    <div class="bloques__grid">
        <div class="bloque">
            <h3 class="bloque__heading">Total de Registrados</h3>
            <div class="bloque__contenido">
                <i class="fa-solid fa-users bloque__icono"></i>
                <p class="bloque__texto"><?php echo $data['totalUsuarios'] ?></p>
            </div>

        </div>
        <div class="bloque">
            <h3 class="bloque__heading">Total de Ingreso</h3>
            <div class="bloque__contenido">
                <i class="fa-solid fa-dollar-sign bloque__icono"></i>
                <p class="bloque__texto">$ <?php echo $data['ingresos']; ?></p>
            </div>
        </div>
        <div class="bloque">
            <h3 class="bloque__heading">Total de Ponentes</h3>
            <div class="bloque__contenido">
                <i class="fa-solid fa-microphone bloque__icono"></i>
                <p class="bloque__texto"><?php echo $data['totalPonentes']; ?></p>
            </div>

        </div>
        <div class="bloque">
            <h3 class="bloque__heading">Últimos Registros</h3>
            <?php if (!empty($registros)) { ?>
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th" scope="col">
                                Nombre
                            </th>
                            <th class="table__th" scope="col">
                                Email
                            </th>
                            <th class="table__th" scope="col">
                                Paquete
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                        <?php foreach ($registros as $registro) { ?>
                            <tr class="table__tr">
                                <td class="table__td">
                                    <?php echo $registro->usuario->nombre . ' ' . $registro->usuario->apellido; ?>
                                </td>
                                <td class="table__td">
                                    <?php echo $registro->usuario->email; ?>
                                </td>
                                <td class="table__td">
                                    <?php echo $registro->paquete->nombre; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
</main>