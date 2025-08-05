// Función auxiliar para enviar formulario por AJAX y actualizar select
async function handleModalForm(formId, url, selectId, valueField, textField) {
    const form = document.getElementById(formId);
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        console.log("URL:", url);
        try {
            const res = await fetch(url, {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                // agregar nueva opción al select
                const select = document.getElementById(selectId);
                const option = document.createElement("option");
                option.value = data[valueField];
                option.textContent = data[textField];
                option.selected = true;
                select.appendChild(option);

                // cerrar modal
                bootstrap.Modal.getInstance(form.closest(".modal")).hide();
                form.reset();
            } else {
                alert("Error: " + (data.message || "No se pudo guardar"));
            }
        } catch (err) {
            alert("Error al enviar formulario: " + err);
        }
    });
}
// Chofer -> espera JSON: { success: true, id_chofer, nombre, apellido }
handleModalForm("formNuevoChofer", _URL + "/chofer/saveAjax", "chofer", "id_chofer", "nombreCompleto");

// Servicio -> espera JSON: { success: true, id_servicio, interno, dominio }
handleModalForm("formNuevoServicio", _URL + "/servicio/saveAjax", "servicio", "id_servicio", "internoDominio");

// Recorrido -> espera JSON: { success: true, id_recorrido, nombre }
handleModalForm("formNuevoRecorrido", _URL + "/recorrido/saveAjax", "recorrido", "id_recorrido", "nombre");


document.addEventListener("DOMContentLoaded", function() {
  const selectRecorrido = document.getElementById("recorrido");
  const accordionRecorrido = document.getElementById("accordionRecorrido");
  const tablaCalles = document.querySelector("#tablaCalles tbody");
  const tablaPuntos = document.querySelector("#tablaPuntos tbody");

  selectRecorrido.addEventListener("change", async function() {
    const idRecorrido = this.value;

    if (!idRecorrido) {
      accordionRecorrido.classList.add("d-none");
      tablaCalles.innerHTML = "";
      tablaPuntos.innerHTML = "";
      return;
    }

    // Mostrar accordion
    accordionRecorrido.classList.remove("d-none");

    // Cargar calles
    try {
      const res = await fetch(`${_URL}/recorrido/calles/${idRecorrido}`);
      const calles = await res.json();

      tablaCalles.innerHTML = "";

      calles.forEach(calle => {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td class="calle-item" data-id="${calle.id_calle}">${calle.nombre}</td>`;
        tablaCalles.appendChild(tr);
      });

      // Reset puntos
      tablaPuntos.innerHTML = "";

    } catch (err) {
      console.error("Error cargando calles:", err);
    }
  });

  // Evento delegado para hacer clic en una calle
  document.querySelector("#tablaCalles").addEventListener("click", async function(e) {
    const td = e.target.closest("td.calle-item");
    if (!td) return;

    const idCalle = td.dataset.id;

    try {
      const res = await fetch(`${_URL}/calle/puntos/${idCalle}`);
      const puntos = await res.json();

      tablaPuntos.innerHTML = "";
      puntos.forEach(p => {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td>${p.nombre}</td>`;
        tablaPuntos.appendChild(tr);
      });
    } catch (err) {
      console.error("Error cargando puntos de detención:", err);
    }
  });
});