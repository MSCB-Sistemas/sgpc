// Función auxiliar para enviar formulario por AJAX y actualizar select
async function handleModalForm(formId, url, selectId, valueField, textField, type, inputID = null) {
  const form = document.getElementById(formId);
  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    try {
      const res = await fetch(url, {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.success) {
        if (type === "select") {
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
      const idCalle = fila.getAttribute('data-id');
      datosCalles.push({ id_calle: idCalle });
    });

    calle_hidden.value = JSON.stringify(datosCalles);
    formPermiso.appendChild(calle_hidden);

    formPermiso.submit(); // Ahora sí enviamos el formulario
  });

  // clave = id_punto, valor = { hotel: X, horario: Y, calle: Z }
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
        tr.dataset.id = calle.id_calle; // <- id en la fila
        tr.innerHTML = `
          <td class="calle-item">${calle.nombre}</td>
          <td><button type="button" class="btn btn-sm btn-danger removeCalle float-end">-</button></td>
        `;
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
        const prev = puntosData[p.id_punto_detencion] || { hotel: "", horario: "", calle: ""};

        // Select de hoteles (lista local)
        let hotelSelect = `<select class="form-select form-select-sm punto-hotel" data-id="${p.id_punto_detencion}" data-calle="${idCalle}">
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
        selectHorario.dataset.calle = idCalle;

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

  tablaCalles.addEventListener("click", async function (e) {
    // obtener idcalle de la fila
    const idCalle = e.target.closest("tr")?.dataset?.id;
    // 1) Si pinchaste el botón (o cualquier hijo del botón)
    const removeBtn = e.target.closest(".removeCalle");
    if (removeBtn) {
      const row = removeBtn.closest("tr");
      for (const id in puntosData) {
        if (puntosData[id].calle === idCalle) {
          delete puntosData[id];
        }
      }
      if (row) row.remove();
      tablaPuntos.innerHTML = "";
      return;
    }
    const td = e.target.closest("td.calle-item");
    if (!td) return;

    // marcar visualmente
    tablaCalles.querySelectorAll("td.calle-item").forEach(el => el.classList.remove("table-active"));
    td.classList.add("table-active");

    if (!idCalle) return; // evita llamar a cargarPuntos(undefined)

    currentCalleId = idCalle;
    await cargarPuntos(idCalle);
  });

  document.getElementById('addCalleForm').addEventListener('click', function () {
    const select = document.getElementById('selectCalleForm');
    const id = select.value;
    const nombre = select.options[select.selectedIndex].text;

    if (!id) return;

    if (document.querySelector(`#tablaCalles tbody tr[data-id="${id}"]`)) {
        alert("Esa calle ya fue agregada.");
        return;
    }

    const tbody = document.querySelector('#tablaCalles tbody');
    const tr = document.createElement('tr');
    tr.setAttribute('data-id', id);
    tr.innerHTML = `
      <td class="calle-item">${nombre}</td>
      <td><button type="button" class="btn btn-sm btn-danger removeCalle float-end">-</button></td>
    `;
    tbody.appendChild(tr);
  });

  // Delegamos cambios en hotel/horario para actualizar puntosData
  tablaPuntos.addEventListener("change", function (e) {
    const select = e.target.closest(".punto-hotel");
    const input = e.target.closest(".punto-horario");

    if (select) {
      const id = select.getAttribute("data-id");
      puntosData[id] = puntosData[id] || {};
      puntosData[id].hotel = select.value;
      puntosData[id].calle = select.getAttribute("data-calle");
    }

    if (input) {
      const id = input.getAttribute("data-id");
      puntosData[id] = puntosData[id] || {};
      puntosData[id].horario = input.value;
      puntosData[id].calle = input.getAttribute("data-calle");
    }
  });

  document.getElementById("btnRefreshPuntos").addEventListener("click", async function () {
    if (currentCalleId) {
      await cargarPuntos(currentCalleId);
    }
  });

  document.getElementById('btnNuevoPunto').addEventListener('click', function (e) {
    e.preventDefault(); // evita que abra el href fijo
    const baseUrl = this.getAttribute('href');
    const url = `${baseUrl}/${currentCalleId}`;
    window.open(url, '_blank'); // abre en nueva pestaña (igual que target="_blank")
  });

});