<template>
  <div>
    <div class="mt-1.5 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
      <button @click="openModal" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Crear Ausencia</button>
    </div>

    <div id="ausencias-container" class="mt-4">
      <div class="text-center py-4">Cargando ausencias...</div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal content -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Crear Nueva Ausencia</h3>
            <div class="mt-2">
              <form @submit.prevent="guardarNuevaAusencia">
                <div class="mt-4">
                  <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                  <input type="date" v-model="newAusencia.fecha" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mt-4">
                  <label for="hora" class="block text-sm font-medium text-gray-700">Hora</label>
                  <input type="time" v-model="newAusencia.hora" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mt-4 flex justify-end">
                  <button @click="closeModal" type="button" class="mr-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancelar</button>
                  <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Guardar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MisAusencias',
  data() {
    return {
      ausencias: [],
      editMode: {},
      newRow: null,
      showModal: false,
      newAusencia: {
        fecha: '',
        hora: ''
      }
    };
  },
  async mounted() {
    await this.fetchAusencias();
  },
  methods: {
    async fetchAusencias() {
      const container = document.getElementById('ausencias-container');
      try {
        const ausencias = await this.getAusencias();
        this.ausencias = ausencias;
        if (ausencias.length === 0) {
          container.innerHTML = '<div class="text-center py-4">No hay ausencias registradas</div>';
        } else {
          this.renderTable();
        }
      } catch (error) {
        container.innerHTML = `<div class="text-center py-4 text-red-500">Error: ${error.message}</div>`;
      }
    },
    async getAusencias() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No se encontró el token de autenticación');
        }

        const response = await fetch('http://127.0.0.1:8080/api/ausencias', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        if (!response.ok) {
          throw new Error(`Failed to fetch ausencias: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        return data;
      } catch (err) {
        console.error('Error fetching ausencias:', err);
        return [];
      }
    },
    renderTable() {
      const container = document.getElementById('ausencias-container');
      const table = document.createElement('table');
      table.className = 'min-w-full bg-white mt-4';

      const thead = document.createElement('thead');
      thead.className = 'bg-gray-800 text-white';
      thead.innerHTML = `
        <tr>
          <th class="w-1/4 px-4 py-2">Fecha</th>
          <th class="w-1/4 px-4 py-2">Hora</th>
          <th class="w-1/4 px-4 py-2">ID</th>
          <th class="w-1/4 px-4 py-2">Acciones</th>
        </tr>
      `;
      table.appendChild(thead);

      const tbody = document.createElement('tbody');
      if (this.newRow) {
        const tr = document.createElement('tr');
        tr.innerHTML = this.getEditableRow(this.newRow, true);
        tbody.appendChild(tr);
      }
      this.ausencias.forEach(ausencia => {
        const tr = document.createElement('tr');
        tr.innerHTML = this.editMode[ausencia.id] ? this.getEditableRow(ausencia) : this.getReadOnlyRow(ausencia);
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);

      container.innerHTML = '';
      container.appendChild(table);

      this.addEventListeners();
    },
    getReadOnlyRow(ausencia) {
      return `
        <td class="border px-4 py-2">${ausencia.fecha}</td>
        <td class="border px-4 py-2">${ausencia.hora}</td>
        <td class="border px-4 py-2">${ausencia.id}</td>
        <td class="border px-4 py-2 flex flex-col space-y-2">
          <button class="editar-btn text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900" data-id="${ausencia.id}">Editar</button>
          <button class="eliminar-btn text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900" data-id="${ausencia.id}">Eliminar</button>
        </td>
      `;
    },
    getEditableRow(ausencia, isNew = false) {
      return `
        <td class="border px-4 py-2">
          <input type="date" value="${ausencia.fecha || ''}">
        </td>
        <td class="border px-4 py-2"><input type="time" value="${ausencia.hora || ''}"></td>
        <td class="border px-4 py-2">${isNew ? 'Nuevo' : ausencia.id}</td>
        <td class="border px-4 py-2 flex flex-col space-y-2">
          <button class="guardar-btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800" data-id="${ausencia.id}">Guardar</button>
          <button class="cancelar-btn text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" data-id="${ausencia.id}">Cancelar</button>
        </td>
      `;
    },
    addEventListeners() {
      document.querySelectorAll('.editar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          this.editMode = { ...this.editMode, [id]: true };
          this.renderTable();
        });
      });

      document.querySelectorAll('.eliminar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          if (confirm('¿Está seguro de que quiere eliminar este registro?')) {
            this.eliminarAusencia(id);
          }
        });
      });

      document.querySelectorAll('.guardar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          if (id === 'Nuevo') {
            this.guardarNuevaAusencia();
          } else {
            this.guardarAusencia(id);
          }
        });
      });

      document.querySelectorAll('.cancelar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          if (id === 'Nuevo') {
            this.newRow = null;
          } else {
            this.editMode = { ...this.editMode, [id]: false };
          }
          this.renderTable();
        });
      });
    },
    openModal() {
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    async guardarNuevaAusencia() {
      const { fecha, hora } = this.newAusencia;

      const date = new Date(fecha);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const formattedDate = `${day}/${month}/${year}`;

      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (date < today) {
        console.error('Fecha inválida: la fecha no puede ser anterior a la fecha actual');
        alert('Fecha inválida. La fecha no puede ser anterior a la fecha actual.');
        return;
      }

      const newAusencia = { 
        user_id: 1, // Reemplazar con el ID de usuario correcto
        fecha: formattedDate, 
        hora: hora || null
      };

      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No se encontró el token de autenticación');
        }

        console.log('Enviando petición con token:', token);
        console.log('Datos enviados:', newAusencia);

        const response = await fetch('http://127.0.0.1:8080/api/ausencias', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(newAusencia)
        });

        console.log('Content-Type:', response.headers.get("content-type"));

        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          const responseText = await response.text();
          console.log('Server response:', responseText);

          if (!response.ok) {
            throw new Error(`Failed to create ausencia: ${response.status} ${response.statusText}. Details: ${responseText}`);
          }

          const createdAusencia = JSON.parse(responseText);
          this.ausencias.push(createdAusencia);
          this.newAusencia = { fecha: '', hora: '' };
          this.closeModal();
          await this.fetchAusencias();  // Actualizar tabla después de crear una nueva ausencia
        } else {
          const responseText = await response.text();
          console.log('Unexpected server response:', responseText);
          throw new Error(`Unexpected server response: ${responseText}`);
        }
      } catch (err) {
        console.error('Error creating ausencia:', err);
        alert(`Error creating ausencia: ${err.message}`);
      }
    },
    async guardarAusencia(id) {
      const row = document.querySelector(`button[data-id="${id}"]`).closest('tr');
      const fechaInput = row.querySelector('input[type="date"]').value;
      const horaInput = row.querySelector('input[type="time"]').value;

      const date = new Date(fechaInput);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const formattedDate = `${day}/${month}/${year}`;

      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (date < today) {
        console.error('Fecha inválida: la fecha no puede ser anterior a la fecha actual');
        alert('Fecha inválida. La fecha no puede ser anterior a la fecha actual.');
        return;
      }

      const updatedFields = {};

      const originalAusencia = this.ausencias.find(a => String(a.id) === String(id));

      if (!originalAusencia) {
        console.error(`Ausencia with id ${id} not found`);
        alert(`Ausencia con id ${id} no encontrada.`);
        return;
      }

      if (fechaInput && originalAusencia.fecha !== formattedDate) {
        updatedFields.fecha = formattedDate;
      }

      if (horaInput && originalAusencia.hora !== horaInput) {
        updatedFields.hora = horaInput;
      }

      if (Object.keys(updatedFields).length === 0) {
        console.log('No changes detected, skipping update.');
        this.editMode = { ...this.editMode, [id]: false };
        return;
      }

      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No se encontró el token de autenticación');
        }

        console.log('Enviando petición con token:', token);
        console.log('Datos enviados:', updatedFields);

        const response = await fetch(`http://127.0.0.1:8080/api/ausencias/${id}`, {
          method: 'PATCH',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(updatedFields)
        });

        console.log('Content-Type:', response.headers.get("content-type"));

        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          const responseText = await response.text();
          console.log('Server response:', responseText);

          if (!response.ok) {
            throw new Error(`Failed to update ausencia: ${response.status} ${response.statusText}. Details: ${responseText}`);
          }

          const updatedAusencia = JSON.parse(responseText);
          const index = this.ausencias.findIndex(a => a.id === id);
          if (index !== -1) {
            this.$set(this.ausencias, index, updatedAusencia);
          }

          this.editMode = { ...this.editMode, [id]: false };

          await this.fetchAusencias();  // Actualizar tabla después de guardar una ausencia
        } else {
          const responseText = await response.text();
          console.log('Unexpected server response:', responseText);
          throw new Error(`Unexpected server response: ${responseText}`);
        }
      } catch (err) {
        console.error('Error updating ausencia:', err);
        alert(`Error updating ausencia: ${err.message}`);
      }
    },
    async eliminarAusencia(id) {
      if (!id) {
        console.error('No id provided for deletion');
        return;
      }
      const token = sessionStorage.getItem('authToken');
      if (!token) {
        throw new Error('No se encontró el token de autenticación');
      }

      console.log('Enviando petición con token:', token);

      fetch(`http://127.0.0.1:8080/api/ausencias/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
      .then(async response => {
        console.log('Content-Type:', response.headers.get("content-type"));

        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          const responseText = await response.text();
          console.log('Server response:', responseText);

          if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}. Details: ${responseText}`);
          }
          return JSON.parse(responseText);
        } else {
          const responseText = await response.text();
          console.log('Unexpected server response:', responseText);
          throw new Error(`Unexpected server response: ${responseText}`);
        }
      })
      .then(async data => {
        console.log('Ausencia eliminada:', data);
        alert('Ausencia eliminada correctamente');

        this.ausencias = this.ausencias.filter(a => a.id !== id);

        await this.fetchAusencias();  // Actualizar tabla después de eliminar una ausencia
      })
      .catch(error => {
        console.error('Error eliminando la ausencia:', error);
        alert(`Error eliminando la ausencia: ${error.message}`);
      });
    }
  }
}
</script>

<style scoped>
/* Agrega tus estilos aquí */
</style>
