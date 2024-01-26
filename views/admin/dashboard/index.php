<h2 class="dashboard__heading"><?php echo $titulo; ?></h2>

<main class="bloques">
    <div class="bloques__grid">
        <div class="bloque">
            <div class="bloque__grid">
                <i class="fa-solid fa-users bloque__icono"></i>
                <h3 class="bloque__heading">Últimos Registros</h3>
            </div>

            <?php foreach ($registros as $registro) { ?>
                <div class="bloque__contenido">
                    <p class="bloque__texto"><?php echo $registro->usuario->nombre . " " . $registro->usuario->apellido; ?></p>
                </div>
            <?php } ?>
        </div>

        <div class="bloque">
            <div class="bloque__grid">
                <i class="fa-solid fa-sack-dollar bloque__icono"></i>
                <h3 class="bloque__heading">Últimos Ingresos</h3>
            </div>
            <div class="bloque__contenido">
                <p class="bloque__texto--cantidad">$ <?php echo $ingresos; ?></p>
            </div>
        </div>

        <div class="bloque">
            <div class="bloque__grid">
                <i class="fa-solid fa-person-circle-plus bloque__icono"></i>
                <h3 class="bloque__heading">Más Asistidos</h3>
            </div>
            <div class="bloque__contenido">
                <?php foreach ($menosDisponibles as $evento) { ?>
                    <p class="bloque__texto"><?php echo $evento->nombre . " - " . $evento->disponibles . " Lugares";  ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="bloque">
            <div class="bloque__grid">
                <i class="fa-solid fa-person-circle-minus bloque__icono"></i>
                <h3 class="bloque__heading">Menos Asistidos</h3>
            </div>
            <div class="bloque__contenido">
                <?php foreach ($masDisponibles as $evento) { ?>
                    <p class="bloque__texto"><?php echo $evento->nombre . " - " . $evento->disponibles . " Lugares";  ?></p>
                <?php } ?>
            </div>
        </div>

</main>