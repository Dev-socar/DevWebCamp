import Swal from "sweetalert2";
(function () {
  let eventos = [];
  const resumen = document.querySelector("#registro-resumen");
  if (resumen) {
    const eventosBoton = document.querySelectorAll(".evento__agregar");
    const descripcion = document.querySelector("#registro-descripcion");
    const formularioRegistro = document.querySelector("#registro");
    formularioRegistro.addEventListener("submit", submitFormulario);
    eventosBoton.forEach((boton) =>
      boton.addEventListener("click", seleccionarEvento)
    );

    function seleccionarEvento({ target }) {
      if (eventos.length < 5) {
        //Deshabilitar evento
        target.disabled = true;
        eventos = [
          ...eventos,
          {
            id: target.dataset.id,
            titulo: target.parentElement
              .querySelector(".evento__nombre")
              .textContent.trim(),
          },
        ];

        mostrarEventos();
      } else {
        Swal.fire({
          title: "Error",
          text: "Máximo 5 eventos",
          icon: "error",
          confirmButtonText: "Aceptar",
          timer: 3000,
        });
      }
    }

    function mostrarEventos() {
      //limpiar html
      limpiarHTML();
      if (eventos.length > 0) {
        descripcion.style.display = "none";
        eventos.forEach((evento) => {
          const eventoDOM = document.createElement("DIV");
          eventoDOM.classList.add("registro__evento");
          const titulo = document.createElement("H3");
          titulo.classList.add("registro__nombre");
          titulo.textContent = evento.titulo;
          const botonEliminar = document.createElement("BUTTON");
          botonEliminar.classList.add("registro__eliminar");
          botonEliminar.innerHTML = `<i class="fa-solid fa-xmark"></i>`;
          botonEliminar.onclick = function () {
            eliminarEvento(evento.id);
          };

          //rendeerizar en el html
          eventoDOM.appendChild(titulo);
          eventoDOM.appendChild(botonEliminar);
          resumen.appendChild(eventoDOM);
        });
      }
    }

    function eliminarEvento(id) {
      eventos = eventos.filter((evento) => evento.id !== id);
      const botonAgregar = document.querySelector(`[data-id="${id}"]`);
      botonAgregar.disabled = false;
      if (eventos.length === 0) {
        descripcion.style.display = "block";
      }
      mostrarEventos();
    }

    function limpiarHTML() {
      while (resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
      }
    }

    async function submitFormulario(e) {
      e.preventDefault();
      //obtener el regalo
      const regaloId = document.querySelector("#regalo").value;
      const eventosId = eventos.map((evento) => evento.id);

      if (eventosId.length === 0 || regaloId === "") {
        Swal.fire({
          title: "Error",
          text: "Escoge al menos un evento y un regalo ",
          icon: "error",
          confirmButtonText: "Aceptar",
          timer: 3000,
        });
        return; //para que no se ejecuten las siguientes líneas
      }
      //Obketo formData
      const datos = new FormData();
      datos.append("eventos", eventosId);
      datos.append("regalo_id", regaloId);
      const url = "/finalizar-registro/conferencias";
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });
        const resultado = await respuesta.json();
        console.log(resultado);
      if (resultado.resultado) {
        Swal.fire(
          "Registro Exitoso",
          "Tus conferencias se han almacendo y tu registro fue exitoso",
          "success"
        ).then(() =>location.href= `/boleto?id=${resultado.token}`)
      } else {
        Swal.fire({
          title: "Error",
          text: "Hubo un error al momento del registro ",
          icon: "error",
          confirmButtonText: "Aceptar",
          timer: 3000,
        }).then(() => location.reload())
      }
    }
  }
})();
