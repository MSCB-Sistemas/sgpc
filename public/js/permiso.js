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
                select.dispatchEvent(new Event('change'));

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

// Lugar -> espera JSON: { success: true, id_recorrido, nombre }
handleModalForm("formNuevoLugar", _URL + "/lugar/saveAjax", "lugar", "id_lugar", "nombre");

// Cargar cales y puntos de detención al seleccionar un recorrido
document.addEventListener("DOMContentLoaded", function() {
  const selectRecorrido = document.getElementById("recorrido");
  const accordionRecorrido = document.getElementById("accordionRecorrido");
  const tablaCalles = document.querySelector("#tablaCalles tbody");
  const tablaPuntos = document.querySelector("#tablaPuntos tbody");
  const formPermiso = document.getElementById("permisoForm");

  document.getElementById("fecha_reserva").addEventListener("change", function() {
    // Limpiar horarios y datos
    document.querySelectorAll(".punto-horario").forEach(sel => {
        sel.innerHTML = `<option value="">Seleccione...</option>`;
    });
    puntosData = {}; // limpiar asignaciones
  });
  
  formPermiso.addEventListener("submit", function(event) {
        event.preventDefault(); // Evita el envío inmediato

        if (confirm("¿Desea imprimir el permiso después de guardarlo?")) {
            // Si el usuario quiere imprimir, agregamos un input oculto
            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "imprimir";
            input.value = "1";
            formPermiso.appendChild(input);
        }
        
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "puntos_detencion";
        hidden.value = JSON.stringify(puntosData);
        formPermiso.appendChild(hidden);

        formPermiso.submit(); // Ahora sí enviamos el formulario
    });

   // clave = id_punto, valor = { hotel: X, horario: Y }
  let puntosData = {};

  const hoteles = window._HOTELES || [];

  selectRecorrido.addEventListener("change", async function() {
    const idRecorrido = this.value;

    if (!idRecorrido) {
      accordionRecorrido.classList.add("d-none");
      tablaCalles.innerHTML = "";
      tablaPuntos.innerHTML = "";
      return;
    }

    accordionRecorrido.classList.remove("d-none");

    try {
      const res = await fetch(`${_URL}/recorrido/calles/${idRecorrido}`);
      const calles = await res.json();

      tablaCalles.innerHTML = "";

      calles.forEach(calle => {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td class="calle-item" data-id="${calle.id_calle}">${calle.nombre}</td>`;
        tablaCalles.appendChild(tr);
      });

      tablaPuntos.innerHTML = ""; // reset puntos al cambiar recorrido
    } catch (err) {
      console.error("Error cargando calles:", err);
    }
  });

  // Evento delegado al hacer click en una calle
  document.querySelector("#tablaCalles").addEventListener("click", async function(e) {
    const td = e.target.closest("td.calle-item");
    if (!td) return;
    document.querySelector("#tablaCalles").querySelectorAll("td").forEach(el => el.classList.remove("table-active"));
    td.classList.toggle("table-active");

    const idCalle = td.dataset.id;
    

    const fecha = document.getElementById("fecha_reserva").value;
    if (!fecha) {
        alert("Seleccione fecha primero");
        return;
    }

    try {
      const res = await fetch(`${_URL}/calle/puntos/${idCalle}`);
      const puntos = await res.json();

      tablaPuntos.innerHTML = "";

      puntos.forEach(async p => {
        // recuperar datos previos si existen
        const prev = puntosData[p.id_punto_detencion] || { hotel: "", horario: "" };

        // armar select hoteles
        let hotelSelect = `<select class="form-select form-select-sm punto-hotel" data-id="${p.id_punto_detencion}">
          <option value="">(ninguno)</option>`;
        hoteles.forEach(h => {
          const selected = (prev.hotel && prev.hotel == h.id_hotel) ? "selected" : "";
          hotelSelect += `<option value="${h.id_hotel}" ${selected}>${h.nombre} - ${h.direccion}</option>`;
        });
        hotelSelect += `</select>`;

        // armar select horario (solo agregamos value si existe)
        let selectHorario = document.createElement("select");
        selectHorario.className = "form-select form-select-sm punto-horario";
        selectHorario.dataset.id = p.id_punto_detencion;
        try {
          const resHorarios = await fetch(`${_URL}/reservaspuntos/horariosDisponibles/${p.id_punto_detencion}/${fecha}`);
          const horarios = await resHorarios.json();

          if (horarios.length === 0) {
              selectHorario.innerHTML = `<option value="">No hay horarios disponibles</option>`;
              return;
          }

          selectHorario.innerHTML = `<option value="">Seleccione...</option>`;
          horarios.forEach(h => {
              const opt = document.createElement("option");
              if (prev.horario && prev.horario == h) {
                opt.selected = true;
              }
              opt.value = h;
              opt.textContent = h;
              selectHorario.appendChild(opt);
          });
        } catch (err) {
            console.error("Error cargando horarios:", err);
        }

        const tdHorario = document.createElement("td");
        tdHorario.appendChild(selectHorario);

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${p.nombre}</td>
          <td>${hotelSelect}</td>
        `;
        tr.appendChild(tdHorario);
        tablaPuntos.appendChild(tr);
      });

    } catch (err) {
      console.error("Error cargando puntos de detención:", err);
    }
  });

  // Delegamos cambios en hotel/horario para actualizar puntosData
  tablaPuntos.addEventListener("change", function(e) {
    const select = e.target.closest(".punto-hotel");
    const input = e.target.closest(".punto-horario");

    if (select) {
      const id = select.getAttribute("data-id");
      puntosData[id] = puntosData[id] || {};
      puntosData[id].hotel = select.value;
    }

    if (input) {
      const id = input.getAttribute("data-id");
      puntosData[id] = puntosData[id] || {};
      puntosData[id].horario = input.value;
    }
  });
});