<h2 class="dashboard__heading"><?php echo $titulo; ?></h2>

<div class="dashboard__contenedor-boton">
    <a class="dashboard__boton" href="/admin/ponentes/crear">
        <i class="fa-solid fa-circle-plus"></i>
        Añadir ponente
    </a>
</div>

<div class="dashboard__contenedor">
    <?php if (!empty($ponentes)) {?>
        <table class="table">
            <thead class="table__thead">
                <tr>
                    <th class="table__th" scope="col">
                        Nombre
                    </th>
                    <th class="table__th" scope="col">
                        Ubicación
                    </th>
                    <th class="table__th" scope="col">

                    </th>
                </tr>
            </thead>
            <tbody class="table__tbody">
                <?php foreach ($ponentes as $ponente) { ?>
                    <tr class="table__tr">
                        <td class="table__td">
                            <?php echo $ponente->nombre . ' ' . $ponente->apellido; ?>
                        </td>
                        <td class="table__td">
                            <?php echo $ponente->ciudad . ', ' . $ponente->pais; ?>
                        </td>
                        <td class="table__td--acciones">
                            <a class="table__accion table__accion--editar" href="/admin/ponentes/editar?id=<?php echo $ponente->id; ?>">
                                <i class="fa-solid fa-user-pen"></i>
                                Editar
                            </a>
                            <form action="/admin/ponentes/eliminar" method="POST" class="table__formulario">
                                <input type="hidden" name="id" value="<?php echo $ponente->id; ?>">
                                <button class="table__accion table__accion--eliminar" type="submit">
                                    <i class="fa-solid fa-user-xmark"></i>
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    <?php } else { ?>

        <p class="text-center">No hay ponentes aún</p>
    <?php } ?>
</div>

<?php echo $paginacion; ?>