// Función auxiliar para enviar formulario por AJAX y actualizar select
async function handleModalForm(formId, url, selectId, valueField, textField, type, inputID=null) {
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
        if (type === "select"){
          // agregar nueva opción al select
          const select = document.getElementById(selectId);
          const option = document.createElement("option");
          option.value = data[valueField];
          option.textContent = data[textField];
          option.selected = true;
          select.appendChild(option);
          select.dispatchEvent(new Event('change'));
        } else if (type === "datalist" && inputID) {
          const datalist = document.getElementById(selectId);
          const input = document.querySelector(`input[list="${selectId}"]`);
          const hidden = document.getElementById(inputID); // ej: id_chofer

          const option = document.createElement("option");
          option.value = data[textField];
          option.dataset.id = data[valueField];
          option.selected = true;
          datalist.appendChild(option);

          // setear input y hidden directamente
          input.value = data[textField];
          hidden.value = data[valueField];
        }
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
handleModalForm("formNuevoChofer", _URL + "/chofer/saveAjax", "choferes", "id_chofer", "nombreCompleto", "datalist", "id_chofer");

// Servicio -> espera JSON: { success: true, id_servicio, interno, dominio }
handleModalForm("formNuevoServicio", _URL + "/servicio/saveAjax", "servicios", "id_servicio", "internoDominio", "datalist", "id_servicio");

// Recorrido -> espera JSON: { success: true, id_recorrido, nombre }
handleModalForm("formNuevoRecorrido", _URL + "/recorrido/saveAjax", "recorrido", "id_recorrido", "nombre", "select");

// Lugar -> espera JSON: { success: true, id_recorrido, nombre }
handleModalForm("formNuevoLugar", _URL + "/lugar/saveAjax", "lugares", "id_lugar", "nombre", "datalist", "id_lugar");



// Cargar cales y puntos de detención al seleccionar un recorrido
document.addEventListener("DOMContentLoaded", function () {
  const selectRecorrido = document.getElementById("recorrido");
  const accordionRecorrido = document.getElementById("accordionRecorrido");
  const tablaCalles = document.querySelector("#tablaCalles tbody");
  const tablaPuntos = document.querySelector("#tablaPuntos tbody");
  const formPermiso = document.getElementById("permisoForm");
  let currentCalleId = null;

  formPermiso.addEventListener("submit", function (event) {
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

    const calle_hidden = document.createElement("input");
    calle_hidden.type = "hidden";
    calle_hidden.name = "calles_permiso";

    const filas = document.querySelectorAll('#tablaCalles tbody tr');
    const datosCalles = [];

    filas.forEach(fila => {
      // Buscar el td que tiene el data-id
      const td = fila.querySelector('td[data-id]');
      if (td) {
          const idCalle = td.getAttribute('data-id');
          datosCalles.push({ id_calle: idCalle });
      }
    });
    
    calle_hidden.value = JSON.stringify(datosCalles);
    formPermiso.appendChild(calle_hidden);

    formPermiso.submit(); // Ahora sí enviamos el formulario
  });

  // clave = id_punto, valor = { hotel: X, horario: Y }
  let puntosData = {};

  const hoteles = window._HOTELES || [];

  selectRecorrido.addEventListener("change", async function () {
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
        tr.innerHTML = `<td class="calle-item" data-id="${calle.id_calle}">${calle.nombre}</td>
          <td class="calle-item"><button type="button" class="btn btn-sm btn-danger removeCalle float-end">-</button></td>`;
        tablaCalles.appendChild(tr);
      });

      tablaPuntos.innerHTML = ""; // reset puntos al cambiar recorrido
    } catch (err) {
      console.error("Error cargando calles:", err);
    }
  });

  async function cargarPuntos(idCalle) {
    const fecha = document.getElementById("fecha_reserva").value;
    if (!fecha) {
      alert("Seleccione fecha primero");
      return;
    }

    try {
      const res = await fetch(`${_URL}/calle/puntos/${idCalle}`);
      const puntos = await res.json();

      // Limpiar la tabla antes de reconstruirla
      tablaPuntos.innerHTML = "";

      for (const p of puntos) {
        const prev = puntosData[p.id_punto_detencion] || { hotel: "", horario: "" };

        // Select de hoteles (lista local)
        let hotelSelect = `<select class="form-select form-select-sm punto-hotel" data-id="${p.id_punto_detencion}">
        <option value="">(ninguno)</option>`;
        hoteles.forEach(h => {
          const selected = (prev.hotel && String(prev.hotel) === String(h.id_hotel)) ? "selected" : "";
          hotelSelect += `<option value="${h.id_hotel}" ${selected}>${h.nombre} - ${h.direccion}</option>`;
        });
        hotelSelect += `</select>`;

        // Select de horarios (depende de fecha)
        const selectHorario = document.createElement("select");
        selectHorario.className = "form-select form-select-sm punto-horario";
        selectHorario.dataset.id = p.id_punto_detencion;

        try {
          const resHorarios = await fetch(`${_URL}/reservaspuntos/horariosDisponibles/${p.id_punto_detencion}/${fecha}`);
          const horarios = await resHorarios.json();

          if (!Array.isArray(horarios) || horarios.length === 0) {
            selectHorario.innerHTML = `<option value="">No hay horarios disponibles</option>`;
          } else {
            selectHorario.innerHTML = `<option value="">Seleccione...</option>`;
            horarios.forEach(h => {
              const opt = document.createElement("option");
              opt.value = h;
              opt.textContent = h;
              if (prev.horario && String(prev.horario) === String(h)) opt.selected = true;
              selectHorario.appendChild(opt);
            });
          }
        } catch (err) {
          console.error("Error cargando horarios:", err);
          selectHorario.innerHTML = `<option value="">Error cargando horarios</option>`;
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
      }

    } catch (err) {
      console.error("Error cargando puntos de detención:", err);
    }
  }

  document.getElementById("fecha_reserva").addEventListener("change", async function () {
    // Limpia selects visibles (si ya hay algo cargado)
    document.querySelectorAll(".punto-horario").forEach(sel => {
      sel.innerHTML = `<option value="">Seleccione...</option>`;
    });
    // Opcional: limpiar la tabla completa para evitar parpadeos de datos viejos
    tablaPuntos.innerHTML = "";

    // Limpiar asignaciones guardadas
    puntosData = {};

    // Si ya había una calle seleccionada, recargar con la nueva fecha
    if (currentCalleId) {
      await cargarPuntos(currentCalleId);
    }
  });

  // Evento delegado al hacer click en una calle
  document.querySelector("#tablaCalles").addEventListener("click", async function (e) {
    const td = e.target.closest("td.calle-item");
    if (!td) return;

    // Marcar visualmente
    document.querySelector("#tablaCalles")
      .querySelectorAll("td.calle-item")
      .forEach(el => el.classList.remove("table-active"));
    td.classList.add("table-active");

    const idCalle = td.dataset.id;
    currentCalleId = idCalle;

    await cargarPuntos(idCalle);
  });

  // Delegamos cambios en hotel/horario para actualizar puntosData
  tablaPuntos.addEventListener("change", function (e) {
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