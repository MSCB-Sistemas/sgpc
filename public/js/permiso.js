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
      // Si el datalist que estamos actualizando es el de 'servicios',
      // usamos la función específica que ya actualiza las variables de estado.
      if (selectId === 'servicios') {
          agregarServicio(data[textField], data[valueField]);
      } else if (selectId === 'lugares') { 
      // Delega al experto en lugares
      agregarLugar(data[textField], data[valueField]);
      } else if (selectId === 'choferes') { 
        // Delega al experto en choferes
        agregarChofer(data[textField], data[valueField]);
      } else {
          // Para otros datalists, mantenemos la lógica genérica (si la hubiera)
          const datalist = document.getElementById(selectId);
          const input = document.querySelector(`input[list="${selectId}"]`);
          const hidden = document.getElementById(inputID);
          
          const option = document.createElement("option");
          option.value = data[textField];
          option.dataset.id = data[valueField];
          datalist.appendChild(option);

          input.value = data[textField];
          hidden.value = data[valueField];
      }
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



/********************************************************************************
  Manejo de formularios modales para agregar Chofer, Servicio, Recorrido, Lugar y Punto
*********************************************************************************/


// Chofer
handleModalForm("formNuevoChofer", _URL + "/chofer/saveAjax", "choferes", "id_chofer", "nombreCompleto", "datalist", "id_chofer");

// Servicio
handleModalForm("formNuevoServicio", _URL + "/servicio/saveAjax", "servicios", "id_servicio", "internoDominio", "datalist", "id_servicio");

// Recorrido
handleModalForm("formNuevoRecorrido", _URL + "/recorrido/saveAjax", "recorrido", "id_recorrido", "nombre", "select");

// Lugar
handleModalForm("formNuevoLugar", _URL + "/lugar/saveAjax", "lugares", "id_lugar", "nombre", "datalist", "id_lugar");

// Punto -> espera JSON: { success: true, id_punto_detencion, nombre }
handleModalForm("formNuevoPunto", _URL + "/PuntosDetencion/saveAjax", "puntos_detencion", "id_punto_detencion", "nombre", "table", "id_punto_detencion");


/********************************************************************************
                                      FIN
*********************************************************************************/



// Cargar cales y puntos de detención al seleccionar un recorrido
document.addEventListener("DOMContentLoaded", function () {
  const selectRecorrido = document.getElementById("recorrido");
  const accordionRecorrido = document.getElementById("accordionRecorrido");
  const tablaCalles = document.querySelector("#tablaCalles tbody");
  const tablaPuntos = document.querySelector("#tablaPuntos tbody");
  const formPermiso = document.getElementById("permisoForm");
  let currentCalleId = null;
  let currentCalleNombre = null;

  // Manejo del envío del formulario
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

    // Antes de enviar, agregamos los datos de puntos y calles como inputs ocultos
    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "puntos_detencion";
    hidden.value = JSON.stringify(puntosData);
    formPermiso.appendChild(hidden);

    // Agregamos las calles del accordion al formulario antes de enviarlo
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

    // Ahora sí enviamos el formulario
    formPermiso.submit(); 
  }); // FIN manejo del envío del formulario
  
  // clave = id_punto, valor = { hotel: X, horario: Y, calle: Z }
  let puntosData = {};

  // obtenemos hoteles del contexto
  const hoteles = window._HOTELES || [];

  // Manejo evento cambio de recorrido (cargar calles)
  selectRecorrido.addEventListener("change", async function () {
    const idRecorrido = this.value;

    // Si no hay recorrido seleccionado, ocultar acordeón y limpiar tablas
    if (!idRecorrido) {
      accordionRecorrido.classList.add("d-none");
      tablaCalles.innerHTML = "";
      tablaPuntos.innerHTML = "";
      return;
    }

    // Mostramos acordeón
    accordionRecorrido.classList.remove("d-none");

    // Traemos las calles del recorrido y las cargamos en la tabla
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
  });// FIN Manejo evento cambio de recorrido (cargar calles)

  // Función para cargar puntos de detención de una calle
  async function cargarPuntos(idCalle) {
    const fecha = document.getElementById("fecha_reserva").value;
    if (!fecha) {
      alert("Seleccione fecha primero");
      return;
    }

    try {
      // Traemos los puntos de detención de la calle de forma asícrona
      const res = await fetch(`${_URL}/calle/puntos/${idCalle}`);
      const puntos = await res.json();

      // Limpiar la tabla antes de reconstruirla
      tablaPuntos.innerHTML = "";

      for (const p of puntos) {
        const prev = puntosData[p.id_punto_detencion] || { hotel: "", horario: "", calle: "" };

        // Buscar nombre del hotel previo (si lo hubiera)
        let prevHotel = hoteles.find(h => String(h.id_hotel) === String(prev.hotel));
        let prevNombre = prevHotel ? `${prevHotel.nombre} - ${prevHotel.direccion}` : "";

        // Armo un select de hoteles por cada punto de detención a cargar
        let datalistId = `hoteles_${p.id_punto_detencion}`;
        let hotelInput = `
          <input type="text" 
            class="form-control form-control-sm punto-hotel-input" 
            list="${datalistId}" 
            data-id="${p.id_punto_detencion}" 
            data-calle="${idCalle}" 
            value="${prevNombre}" 
            placeholder="Seleccione o escriba un hotel...">

          <input type="hidden" 
            name="hotel[${p.id_punto_detencion}]" 
            class="punto-hotel" 
            value="${prev.hotel || ""}">

          <datalist id="${datalistId}">
            ${hoteles.map(h => `<option data-id="${h.id_hotel}" value="${h.nombre} - ${h.direccion}">`).join("")}
          </datalist>
        `;

        // Select de horarios
        const selectHorario = document.createElement("select");
        selectHorario.className = "form-select form-select-sm punto-horario";
        selectHorario.dataset.id = p.id_punto_detencion;
        selectHorario.dataset.calle = idCalle;

        try {
          // Tragio los horarios disponibles para ese punto de detencion en la fecha seleccionada
          const resHorarios = await fetch(`${_URL}/reservasPuntos/horariosDisponibles/${p.id_punto_detencion}/${fecha}`);
          const horarios = await resHorarios.json();

          if (!Array.isArray(horarios) || horarios.length === 0) {
            selectHorario.innerHTML = `<option value="">No hay horarios disponibles</option>`;
          } else {
            // Armo un select con los horarios disponibles encontrados
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

        // Construcción de la fila del punto, con el select de hotel y el de horario
        const tdHorario = document.createElement("td");
        tdHorario.appendChild(selectHorario);

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${p.nombre}</td>
          <td>${hotelInput}</td>
        `;
        tr.appendChild(tdHorario);
        tablaPuntos.appendChild(tr);
      }

    } catch (err) {
      console.error("Error cargando puntos de detención:", err);
    }
  }//FIN cargarPuntos

  // Escucha cualquier cambio en inputs de hotel
  document.addEventListener("input", function(e) {
    if (e.target.classList.contains("punto-hotel-input")) {
      const datalistId = e.target.getAttribute("list");
      const datalist = document.getElementById(datalistId);
      const hidden = e.target.nextElementSibling; // el hidden está justo después

      // IDs asociados al punto de detención
      const puntoId = e.target.dataset.id;
      const calleId = e.target.dataset.calle;

      // Buscar si el texto coincide con alguna opción del datalist
      const option = Array.from(datalist.options).find(opt => opt.value === e.target.value);

      if (option) {
        // Caso: seleccionó un hotel válido del datalist
        hidden.value = option.dataset.id;
        e.target.dataset.lastValidValue = option.value;
        e.target.dataset.lastValidId = option.dataset.id;
        // Guardamos en puntosData
        puntosData[puntoId] = {
          ...(puntosData[puntoId] || {}),
          hotel: option.dataset.id,   // ID del hotel
          calle: calleId,
          horario: puntosData[puntoId]?.horario || "" // preservamos horario si ya estaba
        };
      } else {
        // Caso: escribió texto libre que no corresponde a ningún hotel
        hidden.value = "";

        // Guardamos en puntosData con hotel vacío
        puntosData[puntoId] = {
          ...(puntosData[puntoId] || {}),
          hotel: "",
          calle: calleId,
          horario: puntosData[puntoId]?.horario || ""
        };
      }
    }
  });
  // NUEVO LISTENER: Manejo de restauración para inputs de hotel dinámicos
document.addEventListener('blur', function(e) {
    // Actuamos solo si el usuario sale de un input de hotel
    if (e.target.classList.contains('punto-hotel-input')) {
        const input = e.target;
        const hidden = input.nextElementSibling; // El hidden está justo después
        const datalistId = input.getAttribute('list');
        const datalist = document.getElementById(datalistId);
        
        const currentValue = input.value.trim();
        
        // Verificamos si el valor actual es válido
        const esValido = Array.from(datalist.options).some(opt => opt.value === currentValue);

        // Si el campo está vacío o el valor no es válido...
        if (currentValue === '' || !esValido) {
            // ...buscamos en su "mochila" (dataset) el último valor guardado.
            const lastValidValue = input.dataset.lastValidValue || ''; // Si no hay nada, queda vacío
            const lastValidId = input.dataset.lastValidId || '';

            // Restauramos los valores
            input.value = lastValidValue;
            hidden.value = lastValidId;

            // Si se restauró un valor, actualizamos también puntosData
            if(lastValidValue) {
                const puntoId = input.dataset.id;
                if (puntosData[puntoId]) {
                    puntosData[puntoId].hotel = lastValidId;
                }
            }
        }
    }
}, true); // Usamos 'true' para que el evento se capture de forma más fiable

  // Manejo evento cambio de fecha (recargar puntos si hay calle seleccionada)
  document.getElementById("fecha_reserva").addEventListener("change", async function () {
    // Limpia selects visibles (si ya hay algo cargado)
    document.querySelectorAll(".punto-horario").forEach(sel => {
      sel.innerHTML = `<option value="">Seleccione...</option>`;
    });
    // Limpiar la tabla completa para evitar parpadeos de datos viejos
    tablaPuntos.innerHTML = "";

    // Limpiar asignaciones guardadas
    puntosData = {};

    // Si ya había una calle seleccionada, recargar con la nueva fecha
    if (currentCalleId) {
      await cargarPuntos(currentCalleId);
    }
  });// FIN Manejo evento cambio de fecha

  // Manejo evento click en tabla de calles (seleccionar o eliminar)
  tablaCalles.addEventListener("click", async function (e) {
    // obtener idcalle de la fila
    const idCalle = e.target.closest("tr")?.dataset?.id;
    // Opción 1) Si el clic es en el botón de remover calle (o cualquier hijo del botón)
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
    // Opción 2) Si el clic es en la calle
    const td = e.target.closest("td.calle-item");
    if (!td) return;

    // Marcar visualmente
    tablaCalles.querySelectorAll("td.calle-item").forEach(el => el.classList.remove("table-active"));
    td.classList.add("table-active");

    if (!idCalle) return; // evita llamar a cargarPuntos(undefined)

    currentCalleId = idCalle;
    currentCalleNombre = td.textContent.trim();
    await cargarPuntos(idCalle);
  });// FIN Manejo evento click en tabla de calles

  // Manejo evento click en botón Agregar calle (desde modal)
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
  }); // FIN Manejo evento click en botón Agregar calle (desde modal)

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

  // // Botón para refrescar puntos de detención por si se hizo un cambio por fuera del form
  // document.getElementById("btnRefreshPuntos").addEventListener("click", async function () {
  //   if (currentCalleId) {
  //     await cargarPuntos(currentCalleId);
  //   }
  // });

  // // Botón para abrir en nueva pestaña el formulario de nuevo punto de detención, pasando la calle actual
  // document.getElementById('btnNuevoPunto').addEventListener('click', function (e) {
  //   e.preventDefault(); // evita que abra el href fijo
  //   const baseUrl = this.getAttribute('href');
  //   const url = `${baseUrl}/${currentCalleId}`;
  //   window.open(url, '_blank'); // abre en nueva pestaña (igual que target="_blank")
  // });

  const modalPunto = document.getElementById('modalPunto');

  modalPunto.addEventListener('show.bs.modal', function (event) {
    if (!currentCalleId) {
      alert("Seleccione una calle primero.");
      event.preventDefault();
      return;
    }
    // Setear los campos del modal
    document.getElementById('calle_modal_punto').value = currentCalleNombre;
    document.getElementById('id_calle_modal_punto').value = currentCalleId;
  });

  modalPunto.addEventListener('hidden.bs.modal', async function (event) {
    await cargarPuntos(currentCalleId);
  });

});