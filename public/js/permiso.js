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
