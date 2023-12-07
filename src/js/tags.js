(function () {
  const tagsInput = document.querySelector("#tags_input");
  if (tagsInput) {
    const tagsDiv = document.querySelector("#tags");
    const tagsInputHidden = document.querySelector('[name="tags"]');
    let tags = [];

    //recuperar del input Oculto
    if (tagsInputHidden.value !== "") {
      tags = tagsInputHidden.value.split(",");
      mostrarTags();
    }

    //escuchar los cambios en el input
    tagsInput.addEventListener("keypress", guardarTag);

    function guardarTag(e) {
      if (e.keyCode === 44) {
        if (e.target.value.trim() === "" || e.target.value < 1) {
          return;
        }
        e.preventDefault();
        if (!tags.includes(e.target.value.trim())) {
          tags = [...tags, e.target.value.trim()];
          tagsInput.value = "";
          mostrarTags();
        } else {
          tagsInput.value = "";
        }
      }
    }

    function mostrarTags() {
      tagsDiv.textContent = "";
      tags.forEach((tag) => {
        const etiqueta = document.createElement("LI");
        const btnEliminar = document.createElement("I");
        etiqueta.classList.add("formulario__tag");
        btnEliminar.classList.add("fa-solid");
        btnEliminar.classList.add("fa-x");
        btnEliminar.classList.add("formulario__tag--eliminar");
        etiqueta.textContent = tag;
        btnEliminar.id = etiqueta.textContent;
        btnEliminar.title = "Eliminar Tag";
        btnEliminar.onclick = eliminarTag;
        etiqueta.appendChild(btnEliminar);
        tagsDiv.appendChild(etiqueta);
      });
      actualizarInputHidden();
    }
    function eliminarTag(e) {
      let liEliminar = document.querySelectorAll(".formulario__tag");
      liEliminar.forEach(function (li) {
        if (li.textContent === e.target.id) {
          li.remove();
          e.target.remove();
        }
      });
      tags = tags.filter((tag) => tag !== e.target.id);
      actualizarInputHidden();
      console.log(tags);
    }
    function actualizarInputHidden() {
      tagsInputHidden.value = tags.toString();
    }
  }
})();
